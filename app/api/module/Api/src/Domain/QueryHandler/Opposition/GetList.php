<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Opposition;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * Get a list of Oppositions
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetList extends AbstractQueryHandler
{
    protected $repoServiceName = 'Opposition';

    public function handleQuery(QueryInterface $query)
    {
        /** @var \Dvsa\Olcs\Api\Domain\Repository\Opposition $repo */
        $repo = $this->getRepo();

        return [
            'result' => $this->resultList(
                $repo->fetchList($query, Query::HYDRATE_OBJECT),
                [
                    'application',
                    'case',
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
