<?php

/**
 * Application TransportManagers Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

/**
 * Application TransportManagers Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationTransportManagersReviewService extends AbstractReviewService
{
    /** @var TransportManagersReviewService */
    private $transportManagersReviewService;

    /**
     * Create service instance
     *
     * @param AbstractReviewServiceServices $abstractReviewServiceServices
     * @param TransportManagersReviewService $transportManagersReviewService
     *
     * @return ApplicationTransportManagersReviewService
     */
    public function __construct(
        AbstractReviewServiceServices $abstractReviewServiceServices,
        TransportManagersReviewService $transportManagersReviewService
    ) {
        parent::__construct($abstractReviewServiceServices);
        $this->transportManagersReviewService = $transportManagersReviewService;
    }

    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = [])
    {
        return [
            'subSections' => [
                [
                    'mainItems' => $this->transportManagersReviewService->getConfigFromData($data['transportManagers'])
                ]
            ]
        ];
    }
}
