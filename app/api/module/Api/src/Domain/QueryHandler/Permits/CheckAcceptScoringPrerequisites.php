<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\Query\Permits\CheckAcceptScoringPrerequisites as CheckAcceptScoringPrerequisitesQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Check accept scoring prerequisites
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CheckAcceptScoringPrerequisites extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpPermitRange';

    protected $extraRepos = ['IrhpPermit', 'IrhpCandidatePermit'];

    /**
     * Handle query
     *
     * @param QueryInterface|CheckAcceptScoringPrerequisitesQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $stockId = $query->getId();

        $combinedRangeSize = $this->getRepo()->getCombinedRangeSize($stockId);
        if (is_null($combinedRangeSize)) {
            return $this->generateResponse(
                false,
                'No ranges available in this stock'
            );
        }

        $assignedPermits = $this->getRepo('IrhpPermit')->getPermitCount($stockId);
        $permitsAvailable = $combinedRangeSize - $assignedPermits;
        $permitsRequired = $this->getRepo('IrhpCandidatePermit')->getSuccessfulCountInScope($stockId);

        if ($permitsAvailable < $permitsRequired) {
            return $this->generateResponse(
                false,
                sprintf(
                    'Insufficient permits available - %s available, %s required',
                    $permitsAvailable,
                    $permitsRequired
                )
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
