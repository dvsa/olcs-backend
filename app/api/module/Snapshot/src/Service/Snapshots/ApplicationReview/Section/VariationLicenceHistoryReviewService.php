<?php

/**
 * Variation LicenceHistory Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

/**
 * Variation LicenceHistory Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationLicenceHistoryReviewService extends AbstractReviewService
{
    /** @var ApplicationLicenceHistoryReviewService */
    private $applicationLicenceHistoryReviewService;

    /**
     * Create service instance
     *
     * @param AbstractReviewServiceServices $abstractReviewServiceServices
     * @param ApplicationLicenceHistoryReviewService $applicationLicenceHistoryReviewService
     *
     * @return VariationLicenceHistoryReviewService
     */
    public function __construct(
        AbstractReviewServiceServices $abstractReviewServiceServices,
        ApplicationLicenceHistoryReviewService $applicationLicenceHistoryReviewService
    ) {
        parent::__construct($abstractReviewServiceServices);
        $this->applicationLicenceHistoryReviewService = $applicationLicenceHistoryReviewService;
    }

    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        return $this->applicationLicenceHistoryReviewService->getConfigFromData($data);
    }
}
