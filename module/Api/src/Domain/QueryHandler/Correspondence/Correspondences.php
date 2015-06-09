<?php

/**
 * Correspondences.php
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Correspondence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Correspondences
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class Correspondences extends AbstractQueryHandler
{
    protected $repoServiceName = 'Correspondence';

    public function handleQuery(QueryInterface $query)
    {
        // Object hydration to enforce JsonSerialize.
        $result = $this->getRepo()
            ->fetchList($query, Query::HYDRATE_OBJECT);

        return [
            'result' => $result,
            'count' => $this->getRepo()->fetchCount($query)
        ];
    }
}
