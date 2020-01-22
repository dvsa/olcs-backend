<?php

/**
 * Get a list of Users
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */

namespace Dvsa\Olcs\Api\Domain\QueryHandler\User;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * Get a list of Users
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UserList extends AbstractQueryHandler
{
    protected $repoServiceName = 'User';

    /**
     * Handle query
     *
     * @param QueryInterface $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var User $repo */
        $repo = $this->getRepo();

        return [
            'result' => $this->resultList(
                $repo->fetchList(
                    $query,
                    Query::HYDRATE_OBJECT
                ),
                [
                    'contactDetails' => ['person'],
                    'roles'
                ]
            ),
            'count' => $repo->fetchCount($query)
        ];
    }
}
