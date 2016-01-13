<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Opposition;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Opposition as OppositionRepo;

/**
 * Opposition QueryHandler
 */
final class OppositionList extends AbstractQueryHandler
{
    protected $repoServiceName = 'Opposition';

    public function handleQuery(QueryInterface $query)
    {
        /** @var OppositionRepo $repo */
        $repo = $this->getRepo();
        return [
            'result' => $this->resultList(
                $repo->fetchList($query, Query::HYDRATE_OBJECT),
                [
                    'case' => [
                        'application'
                    ],
                    'grounds',
                    'opposer' => [
                        'contactDetails' => [
                            'person'
                        ]
                    ]
                ]
            ),
            'count' => $repo->fetchCount($query)
        ];
    }
}
