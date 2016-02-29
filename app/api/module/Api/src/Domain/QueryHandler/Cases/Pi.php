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
        'assignedTo',
        'case',
    ];

    public function handleQuery(QueryInterface $query)
    {
        /** @var PiEntity $pi */
        $pi = $this->getRepo()->fetchUsingCase($query, Query::HYDRATE_OBJECT);

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
