<?php

/**
 * BusRegSearchView List
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\BusRegSearchView;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\BusRegSearchView as Repository;
use Dvsa\Olcs\Transfer\Query\Bus\SearchViewList as ListQueryObject;
use Doctrine\ORM\Query as DoctrineQuery;

/**
 * BusRegSearchView List
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class BusRegSearchViewContextList extends AbstractQueryHandler
{
    protected $repoServiceName = 'BusRegSearchView';

    /**
     * Returns a distinct list of column entries identified by query->getContext().
     * Used to populate filter form drop down lists.
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var Repository $repo */
        $repo = $this->getRepo();

        $results = $repo->fetchDistinctList($query);
        return [
            'results' => array_column($results, $query->getContext()),
            'count' => count($results)
        ];
    }
}
