<?php

/**
 * User
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\User;

use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareTrait;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * User
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class User extends AbstractQueryHandler implements OpenAmUserAwareInterface
{
    use OpenAmUserAwareTrait;

    protected $repoServiceName = 'User';

    public function handleQuery(QueryInterface $query)
    {
        if (!$this->isGranted(Permission::CAN_MANAGE_USER_INTERNAL)) {
            throw new ForbiddenException('You do not have permission to manage the record');
        }

        $user = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $user,
            [
                'team',
                'transportManager' => [
                    'homeCd' => [
                        'person'
                    ],
                ],
                'localAuthority',
                'partnerContactDetails',
                'roles',
                'contactDetails' => [
                    'person',
                    'address' => ['countryCode'],
                    'phoneContacts' => ['phoneContactType']
                ],
                'organisationUsers' => [
                    'organisation',
                ],
            ],
            [
                'userType' => $user->getUserType(),
                'lastLoggedInOn' => $this->getOpenAmUser()->fetchUser($user->getPid())['lastLoginTime'] ? : null,
            ]
        );
    }
}
