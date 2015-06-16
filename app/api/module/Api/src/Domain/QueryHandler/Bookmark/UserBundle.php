<?php

/**
 * UserBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * UserBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UserBundle extends AbstractQueryHandler
{
    protected $repoServiceName = 'User';

    public function handleQuery(QueryInterface $query)
    {
        $user = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $user,
            $query->getBundle()
        )->serialize();
    }
}
