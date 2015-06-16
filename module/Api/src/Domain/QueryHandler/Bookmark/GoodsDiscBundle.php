<?php

/**
 * GoodsDiscBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * GoodsDiscBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsDiscBundle extends AbstractQueryHandler
{
    protected $repoServiceName = 'GoodsDisc';

    public function handleQuery(QueryInterface $query)
    {
        $entity = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $entity,
            $query->getBundle()
        )->serialize();
    }
}
