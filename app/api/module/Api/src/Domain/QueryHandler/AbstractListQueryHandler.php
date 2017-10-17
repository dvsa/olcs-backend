<?php

/**
 * Abstract List Query Handler
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler;

use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * Abstract List Query Handler
 */
class AbstractListQueryHandler extends AbstractQueryHandler
{
    protected $bundle = [];

    /**
     * handle list query
     *
     * @param QueryInterface $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $repo = $this->getRepo();

        return [
            'result' => $this->resultList(
                $repo->fetchList($query, Query::HYDRATE_OBJECT),
                $this->bundle
            ),
            'count' => $repo->fetchCount($query)
        ];
    }
}
