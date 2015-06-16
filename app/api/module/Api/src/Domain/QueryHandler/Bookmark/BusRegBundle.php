<?php

/**
 * BusRegBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * BusRegBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusRegBundle extends AbstractQueryHandler
{
    protected $repoServiceName = 'Bus';

    public function handleQuery(QueryInterface $query)
    {
        $busReg = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $busReg,
            $query->getBundle()
        )->serialize();
    }
}
