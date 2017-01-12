<?php

/**
 * Pid
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\User;

use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareTrait;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Pid
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Pid extends AbstractQueryHandler implements OpenAmUserAwareInterface
{
    use OpenAmUserAwareTrait;

    const CAN_RESET_PWD_IF_NOT_ACTIVE_MAX_USER_ID = 1000000;

    protected $repoServiceName = 'User';

    /**
     * Handle query
     *
     * @param QueryInterface $query query
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var UserEntity $user */
        $user = $this->getRepo()->fetchOneByLoginId($query->getId());

        $pid = $user->getPid();

        return [
            'pid' => $pid,
            'canResetPassword' => (
                // migrated selfserve user can always reset a password
                (!$user->isInternal() && ((int)$user->getId() < self::CAN_RESET_PWD_IF_NOT_ACTIVE_MAX_USER_ID))
                // otherwise the user must have logged in before
                || $this->getOpenAmUser()->isActiveUser($pid)
            ),
        ];
    }
}
