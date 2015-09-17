<?php

/**
 * User
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\MyAccount;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
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
        return $this->result(
            $this->getCurrentUser(),
            [
                'team',
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
