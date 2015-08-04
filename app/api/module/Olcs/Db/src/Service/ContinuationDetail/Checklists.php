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
