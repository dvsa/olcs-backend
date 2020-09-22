<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Dvsa\Olcs\Api\Service\Permits\Availability\EmissionsCategoryAvailabilityCounter;

class EmissionsCategoryConditionalAdder
{
    /** @var EmissionsCategoryFactory */
    private $emissionsCategoryFactory;

    /** @var EmissionsCategoryAvailabilityCounter */
    private $emissionsCategoryAvailabilityCounter;

    /**
     * Create service instance
     *
     * @param EmissionsCategoryFactory $emissionsCategoryFactory
     * @param EmissionsCategoryAvailabilityCounter $emissionsCategoryAvailabilityCounter
     *
     * @return EmissionsCategoryConditionalAdder
     */
    public function __construct(
        EmissionsCategoryFactory $emissionsCategoryFactory,
        EmissionsCategoryAvailabilityCounter $emissionsCategoryAvailabilityCounter
    ) {
        $this->emissionsCategoryFactory = $emissionsCategoryFactory;
        $this->emissionsCategoryAvailabilityCounter = $emissionsCategoryAvailabilityCounter;
    }

    /**
     * Add an emissions category to the specified number of permits form representation if the associated ranges are
     * present and contain sufficient free stock
     *
     * @param NoOfPermits $noOfPermits
     * @param string $type
     * @param string|null $value
     * @param string $emissionsCategoryId
     * @param int $stockId
     */
    public function addIfRequired(
        NoOfPermits $noOfPermits,
        $type,
        $value,
        $emissionsCategoryId,
        $stockId
    ) {
        $permitsRemaining = $this->emissionsCategoryAvailabilityCounter->getCount($stockId, $emissionsCategoryId);

        if ($permitsRemaining < 1) {
            return;
        }

        $noOfPermits->addEmissionsCategory(
            $this->emissionsCategoryFactory->create(
                $type,
                $value,
                $permitsRemaining
            )
        );
    }
}
