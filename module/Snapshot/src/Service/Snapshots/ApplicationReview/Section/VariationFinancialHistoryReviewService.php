<?php

/**
 * Variation Financial History Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

/**
 * Variation Financial History Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationFinancialHistoryReviewService extends AbstractReviewService
{
    /** @var ApplicationFinancialHistoryReviewService */
    private $applicationFinancialHistoryReviewService;

    /**
     * Create service instance
     *
     * @param AbstractReviewServiceServices $abstractReviewServiceServices
     * @param ApplicationFinancialHistoryReviewService $applicationFinancialHistoryReviewService
     *
     * @return VariationFinancialHistoryReviewService
     */
    public function __construct(
        AbstractReviewServiceServices $abstractReviewServiceServices,
        ApplicationFinancialHistoryReviewService $applicationFinancialHistoryReviewService
    ) {
        parent::__construct($abstractReviewServiceServices);
        $this->applicationFinancialHistoryReviewService = $applicationFinancialHistoryReviewService;
    }

    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        return $this->applicationFinancialHistoryReviewService->getConfigFromData($data);
    }
}
