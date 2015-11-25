<?php

/**
 * Zfc User
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\User;

use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Zfc User
 *
 * @todo Remove this class when we are fully integrated with OpenAM
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ZfcUser extends AbstractQueryHandler
{
    protected $repoServiceName = 'User';

    public function handleQuery(QueryInterface $query)
    {
        if ($query->getId() !== null) {
            $user = $this->getRepo()->fetchUsingId($query);
        } elseif ($query->getUsername() !== null) {
            $user = $this->getRepo()->fetchOneByLoginId($query->getUsername());
        } else {
            throw new BadRequestException('Missing parameters');
        }

        return $this->result(
            $user,
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
