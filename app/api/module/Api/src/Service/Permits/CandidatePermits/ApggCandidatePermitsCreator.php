<?php

namespace Dvsa\Olcs\Api\Service\Permits\CandidatePermits;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\System\RefData;

class ApggCandidatePermitsCreator
{
    /** @var ApggEmissionsCatCandidatePermitsCreator */
    private $apggEmissionsCatCandidatePermitsCreator;

    /**
     * Create service instance
     *
     * @param ApggEmissionsCatCandidatePermitsCreator $apggEmissionsCatCandidatePermitsCreator
     *
     * @return ApggCandidatePermitsCreator
     */
    public function __construct(ApggEmissionsCatCandidatePermitsCreator $apggEmissionsCatCandidatePermitsCreator)
    {
        $this->apggEmissionsCatCandidatePermitsCreator = $apggEmissionsCatCandidatePermitsCreator;
    }

    /**
     * Create apgg candidate permits as required for the specified application
     *
     * @param IrhpApplication $irhpApplication
     */
    public function create(IrhpApplication $irhpApplication)
    {
        $emissionsCategoryIds = [RefData::EMISSIONS_CATEGORY_EURO5_REF, RefData::EMISSIONS_CATEGORY_EURO6_REF];

        foreach ($emissionsCategoryIds as $emissionsCategoryId) {
            $this->apggEmissionsCatCandidatePermitsCreator->createIfRequired($irhpApplication, $emissionsCategoryId);
        }
    }
}
