<?php

namespace Dvsa\Olcs\Api\Service\Permits\Scoring;

use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;

class ScoringQueryProxy
{
    /** @var RepositoryServiceManager */
    private $repoServiceManager;

    /**
     * Create service instance
     *
     * @param RepositoryServiceManager $repoServiceManager
     *
     * @return ScoringQueryProxy
     */
    public function __construct(RepositoryServiceManager $repoServiceManager)
    {
        $this->repoServiceManager = $repoServiceManager;
    }

    /**
     * Fetch application ids within a stock that are awaiting scoring
     *
     * @param int $stockId
     *
     * @return array
     */
    public function fetchApplicationIdsAwaitingScoring($stockId)
    {
        return $this->getApplicationRepo($stockId)->fetchApplicationIdsAwaitingScoring($stockId);
    }

    /**
     * Fetch application ids within a stock that are both in scope and under consideration
     *
     * @param int $stockId
     *
     * @return array
     */
    public function fetchInScopeUnderConsiderationApplicationIds($stockId)
    {
        return $this->getApplicationRepo($stockId)->fetchInScopeUnderConsiderationApplicationIds($stockId);
    }

    /**
     * Removes the existing scope from the specified stock id
     *
     * @param int $stockId
     */
    public function clearScope($stockId)
    {
        $this->getApplicationRepo($stockId)->clearScope($stockId);
    }

    /**
     * Applies a new scope to the specified stock id
     *
     * @param int $stockId
     */
    public function applyScope($stockId)
    {
        $this->getApplicationRepo($stockId)->applyScope($stockId);
    }

    /**
     * Returns the ids and emissions categories of in scope candidate permits within the specified stock where
     * the associated application has requested the specified sector, ordered by randomised score descending
     *
     * @param int $stockId
     * @param int $sectorsId
     *
     * @return array
     */
    public function getScoreOrderedBySectorInScope($stockId, $sectorsId)
    {
        return $this->getApplicationRepo($stockId)->getScoreOrderedBySectorInScope($stockId, $sectorsId);
    }

    /**
     * Returns the count of candidate permits in the specified stock that are marked as successful and where the
     * associated application relates to a licence for the specified jurisdiction/devolved administration
     *
     * @param int $stockId
     * @param int $jurisdictionId
     *
     * @return int
     */
    public function getSuccessfulDaCountInScope($stockId, $jurisdictionId)
    {
        return $this->getApplicationRepo($stockId)->getSuccessfulDaCountInScope($stockId, $jurisdictionId);
    }

    /**
     * Returns the ids and requested emissions categories of candidate permits within the specified stock that are in
     * scope and unsuccessful, ordered by randomised score descending. Optional parameter to further filter the results
     * by the traffic area of the associated application
     *
     * @param int $stockId
     * @param int $trafficAreaId (optional)
     *
     * @return array
     */
    public function getUnsuccessfulScoreOrderedInScope($stockId, $trafficAreaId = null)
    {
        return $this->getApplicationRepo($stockId)->getUnsuccessfulScoreOrderedInScope($stockId, $trafficAreaId);
    }

    /**
     * Returns the count of candidate permits in the specified stock marked as successful, filtered by emissions
     * category if specified
     *
     * @param int $stockId
     * @param string $assignedEmissionsCategoryId (optional)
     *
     * @return int
     */
    public function getSuccessfulCountInScope($stockId, $assignedEmissionsCategoryId = null)
    {
        return $this->getApplicationRepo($stockId)->getSuccessfulCountInScope($stockId, $assignedEmissionsCategoryId);
    }

    /**
     * Returns the candidate permits in the specified stock marked as successful
     *
     * @param int $stockId
     *
     * @return array
     */
    public function getSuccessfulScoreOrderedInScope($stockId)
    {
        return $this->getApplicationRepo($stockId)->getSuccessfulScoreOrderedInScope($stockId);
    }

    /**
     * Retrieves the ids of candidate permits and corresponding licence numbers in scope for the current scoring run
     *
     * @param int $stockId the Id of the IrhpPermitStock that the scoring will be for
     *
     * @return array a list of candidate permit ids and corresponding licence numbers
     */
    public function fetchDeviationSourceValues($stockId)
    {
        return $this->getApplicationRepo($stockId)->fetchDeviationSourceValues($stockId);
    }

    /**
     * Fetch a flat list of application to country associations within the specified stock
     *
     * @param int $stockId
     *
     * @return array
     */
    public function fetchApplicationIdToCountryIdAssociations($stockId)
    {
        return $this->getApplicationRepo($stockId)->fetchApplicationIdToCountryIdAssociations($stockId);
    }

    /**
     * Retrieves a partial list of column values for the scoring report
     *
     * @param int $stockId the Id of the IrhpPermitStock that the scoring will be for
     *
     * @return array
     */
    public function fetchScoringReport($stockId)
    {
        return $this->getApplicationRepo($stockId)->fetchScoringReport($stockId);
    }

    /**
     * Return the repository instance corresponding to the specified stock id
     *
     * @param int $stockId
     *
     * @return string
     */
    private function getApplicationRepo($stockId)
    {
        $irhpPermitStock = $this->repoServiceManager->get('IrhpPermitStock')->fetchById($stockId);

        if ($irhpPermitStock->getIrhpPermitType()->isEcmtAnnual($stockId)) {
            return $this->repoServiceManager->get('EcmtPermitApplication');
        }

        return $this->repoServiceManager->get('IrhpApplication');
    }
}
