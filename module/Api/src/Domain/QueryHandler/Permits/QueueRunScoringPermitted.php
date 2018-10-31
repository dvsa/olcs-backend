<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\Query\Permits\QueueRunScoringPermitted as QueueRunScoringPermittedQuery;
use Dvsa\Olcs\Api\Domain\Query\Permits\CheckRunScoringPrerequisites;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;

/**
 * Queue run scoring permitted
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class QueueRunScoringPermitted extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpPermitStock';

    /**
     * Handle query
     *
     * @param QueryInterface|QueueRunScoringPermittedQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $stockId = $query->getId();

        $stock = $this->getRepo()->fetchById($query->getId());
        $statusId = $stock->getStatus()->getId();

        $permitted = in_array(
            $statusId,
            [
                IrhpPermitStock::STATUS_SCORING_NEVER_RUN,
                IrhpPermitStock::STATUS_SCORING_SUCCESSFUL,
                IrhpPermitStock::STATUS_SCORING_PREREQUISITE_FAIL,
                IrhpPermitStock::STATUS_SCORING_UNEXPECTED_FAIL
            ]
        );

        if (!$permitted) {
            return $this->generateResponse(
                false,
                'Scoring is not permitted when stock status is ' . $statusId
            );
        }

        $result = $this->getQueryHandler()->handleQuery(
            CheckRunScoringPrerequisites::create(['id' => $stockId])
        );

        return $result;
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
