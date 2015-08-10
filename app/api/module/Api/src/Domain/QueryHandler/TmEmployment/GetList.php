<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\TmEmployment;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * GetList
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetList extends AbstractQueryHandler
{
    protected $repoServiceName = 'TmEmployment';

    public function handleQuery(QueryInterface $query)
    {
        return [
            'result' => $this->resultList(
                $this->getRepo()->fetchList($query, \Doctrine\ORM\Query::HYDRATE_OBJECT),
                [
                    'contactDetails' => [
                        'address' => [
                            'countryCode',
                        ]
                    ]
                ]
            ),
            'count' => $this->getRepo()->fetchCount($query)
        ];
    }
}
