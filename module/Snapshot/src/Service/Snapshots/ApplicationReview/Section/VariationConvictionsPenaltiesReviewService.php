<?php

/**
 * Variation Convictions Penalties Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

/**
 * Variation Convictions Penalties Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationConvictionsPenaltiesReviewService extends AbstractReviewService
{
    /** @var ApplicationConvictionsPenaltiesReviewService */
    private $applicationConvictionsPenaltiesReviewService;

    /**
     * Create service instance
     *
     * @param AbstractReviewServiceServices $abstractReviewServiceServices
     * @param ApplicationConvictionsPenaltiesReviewService $applicationConvictionsPenaltiesReviewService
     *
     * @return VariationConvictionsPenaltiesReviewService
     */
    public function __construct(
        AbstractReviewServiceServices $abstractReviewServiceServices,
        ApplicationConvictionsPenaltiesReviewService $applicationConvictionsPenaltiesReviewService
    ) {
        parent::__construct($abstractReviewServiceServices);
        $this->applicationConvictionsPenaltiesReviewService = $applicationConvictionsPenaltiesReviewService;
    }

    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = [])
    {
        return $this->applicationConvictionsPenaltiesReviewService->getConfigFromData($data);
    }
}
