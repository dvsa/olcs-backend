<?php

/**
 * User
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\MyAccount;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * MyAccount
 */
class MyAccount extends AbstractQueryHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

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
                'contactDetails' => [
                    'person' => ['title'],
                    'address' => ['countryCode'],
                    'phoneContacts' => ['phoneContactType']
                ],
                'organisationUsers' => [
                    'organisation',
                ],
                'roles' => ['role']
            ]
        );
    }
}
