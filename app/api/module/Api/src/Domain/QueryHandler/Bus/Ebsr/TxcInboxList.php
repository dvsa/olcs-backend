<?php

/**
 * TxcInbox List
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\TxcInbox as Repository;
use Doctrine\ORM\Query as DoctrineQuery;

/**
 * TxcInboxList
 */
class TxcInboxList extends AbstractQueryHandler
{
    protected $repoServiceName = 'TxcInbox';

    /**
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var Repository $repo */
        $repo = $this->getRepo();

        return [
            'result' => $this->resultList(
                $repo->fetchList($query, DoctrineQuery::HYDRATE_OBJECT),
                [
                    'busReg'  => [
                        'ebsrSubmissions',
                        'licence' => [
                            'organisation'
                        ],
                        'otherServices'
                    ]
                ]
            ),
            'count' => $repo->fetchCount($query)
        ];
    }
}
