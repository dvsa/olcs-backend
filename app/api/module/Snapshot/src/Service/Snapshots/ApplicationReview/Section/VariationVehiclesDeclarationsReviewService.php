<?php

/**
 * Variation Vehicles Declarations Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

/**
 * Variation Vehicles Declarations Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationVehiclesDeclarationsReviewService extends AbstractReviewService
{
    /** @var ApplicationVehiclesDeclarationsReviewService */
    private $applicationVehiclesDeclarationsReviewService;

    /**
     * Create service instance
     *
     * @param AbstractReviewServiceServices $abstractReviewServiceServices
     * @param ApplicationVehiclesDeclarationsReviewService $applicationVehiclesDeclarationsReviewService
     *
     * @return VariationVehiclesDeclarationsReviewService
     */
    public function __construct(
        AbstractReviewServiceServices $abstractReviewServiceServices,
        ApplicationVehiclesDeclarationsReviewService $applicationVehiclesDeclarationsReviewService
    ) {
        parent::__construct($abstractReviewServiceServices);
        $this->applicationVehiclesDeclarationsReviewService = $applicationVehiclesDeclarationsReviewService;
    }

    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        return $this->applicationVehiclesDeclarationsReviewService->getConfigFromData($data);
    }
}
