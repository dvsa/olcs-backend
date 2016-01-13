<?php

/**
 * User Selfserve
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\User;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * User Selfserve
 */
class UserSelfserve extends AbstractQueryHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'User';

    public function handleQuery(QueryInterface $query)
    {
        $user = $this->getRepo()->fetchUsingId($query);

        if (!$this->isGranted(Permission::CAN_MANAGE_USER_SELFSERVE, $user)
            && !$this->isGranted(Permission::CAN_READ_USER_SELFSERVE, $user)
        ) {
            throw new ForbiddenException('You do not have permission to manage the record');
        }

        return $this->result(
            $user,
            [
                'transportManager',
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
                'permission' => $user->getPermission()
            ]
        );
    }
}
