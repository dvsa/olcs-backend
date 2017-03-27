<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\MyAccount;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * MyAccount
 */
class MyAccount extends AbstractQueryHandler
{
    public function handleQuery(QueryInterface $query)
    {
        $user = $this->getCurrentUser();

        if ($user === null) {
            throw new NotFoundException('No user currently logged in');
        }

        return $this->result(
            $user,
            [
                'team',
                'transportManager',
                'partnerContactDetails',
                'localAuthority',
                'contactDetails' => [
                    'person' => ['title'],
                    'address' => ['countryCode'],
                    'phoneContacts' => ['phoneContactType']
                ],
                'organisationUsers' => [
                    'organisation',
                ],
                'roles' => ['role']
            ],
            [
                'hasActivePsvLicence' => $user->hasActivePsvLicence(),
                'numberOfVehicles' => $user->getNumberOfVehicles(),
            ]
        );
    }
}
