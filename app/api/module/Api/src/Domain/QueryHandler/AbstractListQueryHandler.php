<?php

/**
 * Abstract List Query Handler
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * Abstract List Query Handler
 */
class AbstractListQueryHandler extends AbstractQueryHandler
{
    protected $bundle = [];

    /**
     * @param QueryInterface $query
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
