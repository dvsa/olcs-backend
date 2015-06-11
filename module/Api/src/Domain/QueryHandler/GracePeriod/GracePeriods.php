<?php

/**
 * GracePeriods.php
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\GracePeriod;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Grace Periods
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class GracePeriods extends AbstractQueryHandler
{
    protected $repoServiceName = 'GracePeriod';

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
