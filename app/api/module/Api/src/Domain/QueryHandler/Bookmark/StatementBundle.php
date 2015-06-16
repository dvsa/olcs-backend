<?php

/**
 * StatementBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * StatementBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class StatementBundle extends AbstractQueryHandler
{
    protected $repoServiceName = 'Statement';

    public function handleQuery(QueryInterface $query)
    {
        $statement = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $statement,
            $query->getBundle()
        )->serialize();
    }
}
