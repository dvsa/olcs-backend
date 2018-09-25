<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\Query\Permits\CheckRunScoringPrerequisites as CheckRunScoringPrerequisitesQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Check run scoring prerequisites
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CheckRunScoringPrerequisites extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpPermitRange';

    protected $extraRepos = ['IrhpPermitWindow', 'IrhpPermit'];

    /**
     * Handle query
     *
     * @param QueryInterface|CheckRunScoringPrerequisitesQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $stockId = $query->getId();

        $lastOpenWindow = $this->getRepo('IrhpPermitWindow')->fetchLastOpenWindowByStockId($stockId);
        if (!is_null($lastOpenWindow)) {
            return $this->generateResponse(
                false,
                'A window is currently open within the stock'
            );
        }

        $combinedRangeSize = $this->getRepo()->getCombinedRangeSize($stockId);
        if (is_null($combinedRangeSize)) {
            return $this->generateResponse(
                false,
                'No ranges available in this stock'
            );
        }

        $assignedPermits = $this->getRepo('IrhpPermit')->getPermitCount($stockId);
        $permitsAvailable = $combinedRangeSize - $assignedPermits;
        if ($permitsAvailable < 1) {
            return $this->generateResponse(
                false,
                'No free permits available within the stock'
            );
        }

        return $this->generateResponse(true, 'Prerequisites passed');
    }

    /**
     * Generate an array representing the query response
     *
     * @param bool $permitted
     * @param string $message
     *
     * @return array
     */
    private function generateResponse($permitted, $message)
    {
        return [
            'result' => $permitted,
            'message' => $message
        ];
    }
}
