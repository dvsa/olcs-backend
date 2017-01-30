<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\ContinuationDetail;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail as ContinuationDetailEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Process ContinuationDetail
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class Process extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'ContinuationDetail';

    protected $extraRepos = ['Document', 'FeeType', 'Fee'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $continuationDetail = $this->getRepo()->fetchUsingId($command);

        if ($continuationDetail->getStatus()->getId() !== ContinuationDetailEntity::STATUS_PRINTING) {
            $result
                ->addId('continuationDetail', $continuationDetail->getId())
                ->addMessage('Continuation detail no longer pending');
            return $result;
        }

        // 1. Generate the checklist document
        $result->merge($this->generateDocument($continuationDetail, $command->getUser()));

        // 2. Update continuation detail record with the checklist document
        // reference and 'printed' status
        $document = $this->getRepo('Document')->fetchById($result->getId('document'));
        $status = $this->getRepo()->getRefdataReference(ContinuationDetailEntity::STATUS_PRINTED);
        $continuationDetail
            ->setChecklistDocument($document)
            ->setStatus($status);
        $this->getRepo()->save($continuationDetail);
        $result
            ->addId('continuationDetail', $continuationDetail->getId())
            ->addMessage('ContinuationDetail updated');

        // 3. Create the continuation fee, if applicable
        $result->merge($this->createFee($continuationDetail));

        return $result;
    }

    /**
     * @param ContinuationDetailEntity $continuationDetail
     * @param int $user
     * @return Result
     */
    protected function generateDocument(ContinuationDetailEntity $continuationDetail, $user)
    {
        $template = $this->getTemplate($continuationDetail);

        $licence = $continuationDetail->getLicence();

        $dtoData = [
            'template' => $template,
            'query' => [
                'licence' => $licence->getId(),
                'goodsOrPsv' => $licence->getGoodsOrPsv()->getId(),
                'licenceType' => $licence->getLicenceType()->getId(),
                'niFlag' => $licence->getNiFlag(),
                'organisation' => $licence->getOrganisation()->getId(),
                'user' => $user
            ],
            'description' => 'Continuation checklist',
            'licence' => $continuationDetail->getLicence()->getId(),
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_CONTINUATIONS_AND_RENEWALS_LICENCE,
            'isExternal'  => false,
            'isScan' => false,
            'dispatch' => true
        ];

        return $this->handleSideEffect(GenerateAndStore::create($dtoData));
    }

    /**
     * Get the template ID for the checklist document
     *
     * @param ContinuationDetailEntity $continuationDetail
     *
     * @return int
     */
    protected function getTemplate($continuationDetail)
    {
        /* @var $licence LicenceEntity */
        $licence = $continuationDetail->getLicence();

        if ($licence->isGoods()) {
            $template = ($licence->getNiFlag() === 'N') ?
                Document::GV_CONTINUATION_CHECKLIST :
                Document::GV_CONTINUATION_CHECKLIST_NI;
        } else {
            $template = ($licence->getLicenceType()->getId() === LicenceEntity::LICENCE_TYPE_SPECIAL_RESTRICTED) ?
                Document::PSV_CONTINUATION_CHECKLIST_SR :
                Document::PSV_CONTINUATION_CHECKLIST;
        }

        return $template;
    }

    /**
     * @param ContinuationDetailEntity $continuationDetail
     * @return Result
     */
    protected function createFee(ContinuationDetailEntity $continuationDetail)
    {
        $result = new Result();

        $licence = $continuationDetail->getLicence();

        if ($this->shouldCreateFee($licence)) {

            $now = new DateTime();

            $feeType = $this->getRepo('FeeType')->fetchLatest(
                $this->getRepo()->getRefdataReference(FeeTypeEntity::FEE_TYPE_CONT),
                $licence->getGoodsOrPsv(),
                $licence->getLicenceType(),
                $now,
                $licence->getTrafficArea()
            );

            $amount = ($feeType->getFixedValue() != 0 ? $feeType->getFixedValue() : $feeType->getFiveYearValue());

            $data = [
                'feeType' => $feeType->getId(),
                'amount' => $amount,
                'invoicedDate' => $now->format('Y-m-d'),
                'licence' => $licence->getId(),
                'description' => $feeType->getDescription() . ' for licence ' . $licence->getLicNo(),
            ];

            $result = $this->handleSideEffect(CreateFee::create($data));
        }

        return $result;
    }

    /**
     * We want to create a fee if the licence type is goods, or psv special restricted
     * and there is no existing CONT fee
     *
     * @param LicenceEntity $licence
     * @return boolean
     */
    protected function shouldCreateFee(LicenceEntity $licence)
    {
        // If PSV and not SR then we don't need to create a fee
        if ($licence->isPsv() && !$licence->isSpecialRestricted()) {
            return false;
        }

        // check for fees less than three months old
        $after = (new DateTime('now'))->sub(new \DateInterval('P3M'));
        $results = $this->getRepo('Fee')->fetchOutstandingContinuationFeesByLicenceId(
            $licence->getId(),
            $after,
            true
        );

        return empty($results);
    }
}
