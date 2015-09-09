<?php

/**
 * User
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\User;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * User
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class User extends AbstractQueryHandler
{
    protected $repoServiceName = 'User';

    public function handleQuery(QueryInterface $query)
    {
        $user = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $user,
            [
                'team',
                'transportManager',
                'localAuthority',
                'partnerContactDetails',
                'userRoles' => [
                    'role'
                ],
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
                'userType' => $user->getUserType()
            ]
        );
    }
}
