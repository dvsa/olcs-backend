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
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;

/**
 * Get a list of Users
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class UserListInternal extends AbstractQueryHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'User';

    public function handleQuery(QueryInterface $query)
    {
        if (!$this->isGranted(Permission::INTERNAL_USER)) {
            throw new ForbiddenException('You do not have permission access this data');
        }

        $repo = $this->getRepo();

        return [
            'result' => $this->resultList(
                $repo->fetchInternalList(
                    $query,
                    Query::HYDRATE_OBJECT
                ),
                [
                    'team',
                    'contactDetails' => [
                        'person'
                    ]
                ]
            ),
            'count' => $repo->fetchCount($query)
        ];
    }
}
