<?php

/**
 * Variation Vehicles Psv Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

/**
 * Variation Vehicles Psv Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationVehiclesPsvReviewService extends AbstractReviewService
{
    /** @var VehiclesPsvReviewService */
    private $vehiclesPsvReviewService;

    /**
     * Create service instance
     *
     * @param AbstractReviewServiceServices $abstractReviewServiceServices
     * @param VehiclesPsvReviewService $vehiclesPsvReviewService
     *
     * @return VariationVehiclesPsvReviewService
     */
    public function __construct(
        AbstractReviewServiceServices $abstractReviewServiceServices,
        VehiclesPsvReviewService $vehiclesPsvReviewService
    ) {
        parent::__construct($abstractReviewServiceServices);
        $this->vehiclesPsvReviewService = $vehiclesPsvReviewService;
    }

    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        return [
            'subSections' => [
                [
                    'mainItems' => $this->vehiclesPsvReviewService->getConfigFromData($data, [])
                ]
            ]
        ];
    }
}
