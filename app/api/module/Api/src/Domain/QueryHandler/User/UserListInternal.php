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
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class UserListInternal extends AbstractQueryHandler
{
    protected $repoServiceName = 'User';

    public function handleQuery(QueryInterface $query)
    {
        $repo = $this->getRepo();

        return [
            'result' => $this->resultList(
                $repo->fetchList(
                    $query,
                    Query::HYDRATE_OBJECT
                ),
                [
                    'team',
                    'contactDetails' => [
                        'person'
                    ],
                    'roles',
                ]
            ),
            'count' => $repo->fetchCount($query)
        ];
    }
}
