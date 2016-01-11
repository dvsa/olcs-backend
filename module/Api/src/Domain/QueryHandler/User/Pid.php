<?php

/**
 * Pid
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\User;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Pid
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Pid extends AbstractQueryHandler
{
    protected $repoServiceName = 'User';

    public function handleQuery(QueryInterface $query)
    {
        /** @var UserEntity $user */
        $user = $this->getRepo()->fetchOneByLoginId($query->getId());

        return [
            'pid' => $user->getPid()
        ];
    }
}
