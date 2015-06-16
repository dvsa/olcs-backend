<?php

/**
 * TransportManager Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * TransportManager Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerBundle extends AbstractQueryHandler
{
    protected $repoServiceName = 'TransportManager';

    public function handleQuery(QueryInterface $query)
    {
        $tm = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $tm,
            $query->getBundle()
        )->serialize();
    }
}
