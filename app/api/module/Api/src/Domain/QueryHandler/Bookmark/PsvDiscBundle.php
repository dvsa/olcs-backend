<?php

/**
 * PsvDiscBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * PsvDiscBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvDiscBundle extends AbstractQueryHandler
{
    protected $repoServiceName = 'PsvDisc';

    public function handleQuery(QueryInterface $query)
    {
        $entity = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $entity,
            $query->getBundle()
        )->serialize();
    }
}
