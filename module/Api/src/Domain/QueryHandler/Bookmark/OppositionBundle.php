<?php

/**
 * OppositionBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * OppositionBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OppositionBundle extends AbstractQueryHandler
{
    protected $repoServiceName = 'Opposition';

    public function handleQuery(QueryInterface $query)
    {
        $opposition = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $opposition,
            $query->getBundle()
        )->serialize();
    }
}
