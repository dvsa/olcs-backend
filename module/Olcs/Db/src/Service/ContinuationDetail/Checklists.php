<?php

/**
 * Checklists
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Olcs\Db\Service\ContinuationDetail;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Fee\Fee;

/**
 * Checklists
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Checklists implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const CONTINUATION_DETAIL_STATUS_PRINTING = 'con_det_sts_printing';
    const CONTINUATION_DETAIL_STATUS_PRINTED = 'con_det_sts_printed';
    const MESSAGE_TYPE_CONTINUATION_CHECKLIST = 'que_typ_cont_checklist';
    const MESSAGE_STATUS_QUEUED = 'que_sts_queued';

    const LICENCE_TYPE_SPECIAL_RESTRICTED = 'ltyp_sr';
    const LICENCE_CATEGORY_GOODS_VEHICLE = 'lcat_gv';
    const LICENCE_CATEGORY_PSV = 'lcat_psv';

    const FEE_STATUS_OUTSTANDING = 'lfs_ot';
    const FEE_STATUS_WAIVE_RECOMMENDED = 'lfs_wr';

    const FEE_TYPE_CONT = 'CONT';

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Generate checklists
     *
     * @param array $data
     */
    public function generate($data)
    {
        if (empty($data)) {
            return 'No ids provided';
        }

        $results = $this->getServiceLocator()->get('EntityManagerHelper')
            ->findByIds('\Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail', $data);

        if (count($results) < count($data)) {
            return 'Could not find one or more continuation detail records';
        }

        $em = $this->getEntityManager();
        $em->beginTransaction();

        try {

            $this->processResults($results);

            $em->flush();
            $em->commit();

            return true;
        } catch (\Exception $ex) {
            $em->rollback();
            return $ex;
        }
    }

    public function update($id, $data)
    {
        $docId = $data['docId'];

        $em = $this->getEntityManager();
        $emh = $this->getServiceLocator()->get('EntityManagerHelper');

        $qb = $em->createQueryBuilder();

        $qb->select('cd')
            ->from('\Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail', 'cd')
            ->leftJoin('cd.licence', 'l')
            ->where($qb->expr()->eq('cd.id', ':id'))
            ->setParameter('id', $id)
            ->setMaxResults(1);

        $results = $qb->getQuery()->execute();

        if (empty($results)) {
            return 'Continuation detail not found';
        }

        $qb->select('d')
            ->from(Document::class, 'd')
            ->where($qb->expr()->eq('d.id', ':id'))
            ->setParameter('id', $docId)
            ->setMaxResults(1);

        $documents = $qb->getQuery()->execute();

        if (empty($documents)) {
            return 'Document record not found';
        }

        /* @var \Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail $continuationDetail */
        $continuationDetail = $results[0];
        $licence = $continuationDetail->getLicence();

        /* @var $document \Dvsa\Olcs\Api\Entity\Doc\Document */
        $document = $documents[0];

        $em->beginTransaction();

        try {
            // Create the fee
            if ($this->shouldCreateFee($licence)) {
                $feeType = $this->getLatestFeeType(self::FEE_TYPE_CONT, $licence);

                $amount = ($feeType->getFixedValue() != 0 ? $feeType->getFixedValue() : $feeType->getFiveYearValue());

                $feeStatusRefData = $emh->getRefDataReference(self::FEE_STATUS_OUTSTANDING);

                $fee = new Fee($feeType, $amount, $feeStatusRefData);
                $fee->setLicence($licence);
                $fee->setInvoicedDate(new \DateTime());
                $fee->setDescription($feeType->getDescription() . ' for licence ' . $licence->getLicNo());

                $em->persist($fee);
            }

            // Update the continuation detail status
            $printedRefData = $emh->getRefDataReference(self::CONTINUATION_DETAIL_STATUS_PRINTED);
            $continuationDetail->setStatus($printedRefData);
            $continuationDetail->setChecklistDocument($document);

            $em->persist($continuationDetail);

            $em->flush();
            $em->commit();
            return true;
        } catch (\Exception $ex) {
            $em->rollback();
            return $ex;
        }
    }

    /**
     * @return \Dvsa\Olcs\Api\Entity\Fee\FeeType
     */
    protected function getLatestFeeType($feeType, Licence $licence)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $effectiveFrom = (new \DateTime())->format(\DateTime::W3C);

        $qb->select('ft')
            ->from('\Dvsa\Olcs\Api\Entity\Fee\FeeType', 'ft')
            ->where($qb->expr()->eq('ft.feeType', ':feeType'))
            ->andWhere($qb->expr()->eq('ft.goodsOrPsv', ':goodsOrPsv'))
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('ft.licenceType', ':licenceType'),
                    $qb->expr()->isNull('ft.licenceType')
                )
            )
            ->andWhere($qb->expr()->lte('ft.effectiveFrom', ':effectiveFrom'))
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('ft.trafficArea', ':trafficArea'),
                    $qb->expr()->isNull('ft.trafficArea')
                )
            )
            // Send the NULL values to the bottom
            ->orderBy('ft.trafficArea', 'DESC')
            ->addOrderBy('ft.effectiveFrom', 'DESC')
            ->setParameter('trafficArea', $licence->getTrafficArea()->getId())
            ->setParameter('goodsOrPsv', $licence->getGoodsOrPsv()->getId())
            ->setParameter('licenceType', $licence->getLicenceType()->getId())
            ->setParameter('feeType', $feeType)
            ->setParameter('effectiveFrom', $effectiveFrom)
            ->setMaxResults(1);

        $results = $qb->getQuery()->execute();

        if (empty($results)) {
            return null;
        }

        return $results[0];
    }

    /**
     * We want to create a fee if the licence type is goods, or psv special restricted
     * and there is no existing CONT fee
     *
     * @param Licence $licence
     * @return boolean
     */
    protected function shouldCreateFee(Licence $licence)
    {
        // If PSV and not SR then we don't need to create a fee
        if ($licence->getGoodsOrPsv()->getId() === self::LICENCE_CATEGORY_PSV
            && $licence->getLicenceType()->getId() !== self::LICENCE_TYPE_SPECIAL_RESTRICTED) {
            return false;
        }

        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();

        $qb->select('f')
            ->from('\Dvsa\Olcs\Api\Entity\Fee\Fee', 'f')
            ->innerJoin('f.feeType', 'ft')
            ->where($qb->expr()->eq('f.licence', ':licence'))
            ->andWhere(
                $qb->expr()->in('f.feeStatus', [self::FEE_STATUS_OUTSTANDING, self::FEE_STATUS_WAIVE_RECOMMENDED])
            )
            ->andWhere($qb->expr()->eq('ft.feeType', ':feeType'))
            ->setParameter('feeType', self::FEE_TYPE_CONT)
            ->setParameter('licence', $licence->getId())
            ->setMaxResults(1);

        $results = $qb->getQuery()->getArrayResult();

        return empty($results);
    }

    protected function processResults($results)
    {
        $em = $this->getEntityManager();
        $emh = $this->getServiceLocator()->get('EntityManagerHelper');

        $printingRefData = $emh->getRefDataReference(self::CONTINUATION_DETAIL_STATUS_PRINTING);
        $messageTypeRefData = $emh->getRefDataReference(self::MESSAGE_TYPE_CONTINUATION_CHECKLIST);
        $messageStatusRefData = $emh->getRefDataReference(self::MESSAGE_STATUS_QUEUED);

        foreach ($results as $row) {
            $row->setStatus($printingRefData);

            $queueMessage = new Queue();
            $queueMessage->setType($messageTypeRefData);
            $queueMessage->setEntityId($row->getId());
            $queueMessage->setStatus($messageStatusRefData);

            $em->persist($row);
            $em->persist($queueMessage);
        }
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        if ($this->em === null) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }

        return $this->em;
    }
}
