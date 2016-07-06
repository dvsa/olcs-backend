<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Pi\Pi as PiEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * PI
 */
final class Pi extends AbstractQueryHandler
{
    protected $repoServiceName = 'Pi';

    protected $bundle = [
        'piStatus',
        'piTypes',
        'reasons',
        'piHearings' => [
            'presidingTc',
            'presidedByRole',
            'venue'
        ],
        'writtenOutcome',
        'decidedByTc',
        'agreedByTc',
        'decidedByTcRole',
        'agreedByTcRole',
        'decisions',
        'tmDecisions',
        'assignedTo',
        'case',
    ];

    /**
     * Handle query
     *
     * @param QueryInterface $query query
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     */
    public function handleQuery(QueryInterface $query)
    {
        $repo = $this->getRepo();

        // retrieve reason even if deleted
        $repo->disableSoftDeleteable(
            [
                \Dvsa\Olcs\Api\Entity\Pi\Reason::class
            ]
        );

        /** @var PiEntity $pi */
        $pi = $repo->fetchUsingCase($query, Query::HYDRATE_OBJECT);

        if ($pi === null) {
            return [];
        }

        return $this->result(
            $pi,
            $this->bundle,
            $pi->flattenSlaTargetDates()
        );
    }
}
