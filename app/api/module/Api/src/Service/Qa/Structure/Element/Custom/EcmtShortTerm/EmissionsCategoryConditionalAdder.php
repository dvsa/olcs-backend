<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as IrhpPermitRangeRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepository;

class EmissionsCategoryConditionalAdder
{
    /** @var EmissionsCategoryFactory */
    private $emissionsCategoryFactory;

    /** @var IrhpPermitRangeRepository */
    private $irhpPermitRangeRepo;

    /** @var IrhpPermitRepository */
    private $irhpPermitRepo;

    /**
     * Create service instance
     *
     * @param EmissionsCategoryFactory $emissionsCategoryFactory
     * @param IrhpPermitRangeRepository $irhpPermitRangeRepo
     * @param IrhpPermitRepository $irhpPermitRepo
     *
     * @return EmissionsCategoryConditionalAdder
     */
    public function __construct(
        EmissionsCategoryFactory $emissionsCategoryFactory,
        IrhpPermitRangeRepository $irhpPermitRangeRepo,
        IrhpPermitRepository $irhpPermitRepo
    ) {
        $this->emissionsCategoryFactory = $emissionsCategoryFactory;
        $this->irhpPermitRangeRepo = $irhpPermitRangeRepo;
        $this->irhpPermitRepo = $irhpPermitRepo;
    }

    /**
     * Add an emissions category to the specified number of permits form representation if the associated ranges are
     * present and contain sufficient free stock
     *
     * @param NoOfPermits $noOfPermits
     * @param string $fieldName
     * @param string $labelTranslationKey
     * @param string|null $value
     * @param string $emissionsCategoryId
     * @param int $stockId
     */
    public function addIfRequired(
        NoOfPermits $noOfPermits,
        $fieldName,
        $labelTranslationKey,
        $value,
        $emissionsCategoryId,
        $stockId
    ) {
        $combinedRangeSize = $this->irhpPermitRangeRepo->getCombinedRangeSize($stockId, $emissionsCategoryId);

        if (is_null($combinedRangeSize)) {
            return;
        }

        // TODO: this logic will also need to take candidate permits into account
        // something like candidate permits assigned to a stock of the specified emissions type and belong
        // to an application in status GRANTED
        $permitCount = $this->irhpPermitRepo->getPermitCount($stockId, $emissionsCategoryId);
        $permitsRemaining = $combinedRangeSize - $permitCount;
        
        if ($permitsRemaining < 1) {
            return;
        }

        $noOfPermits->addEmissionsCategory(
            $this->emissionsCategoryFactory->create(
                $fieldName,
                $labelTranslationKey,
                $value,
                $permitsRemaining
            )
        );
    }
}
