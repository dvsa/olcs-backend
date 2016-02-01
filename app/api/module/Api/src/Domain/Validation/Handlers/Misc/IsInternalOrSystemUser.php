<?php

/**
 * Is Internal or System User
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider as PidIdentityProviderEntity;

/**
 * Is Internal or System User
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class IsInternalOrSystemUser extends AbstractHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    /**
     * @inheritdoc
     */
    public function isValid($dto)
    {
        $team = ($this->getUser() && $this->getUser()->getTeam()) ? $this->getUser()->getTeam()->getId() : null;
        return $this->isInternalUser() || $team === PidIdentityProviderEntity::SYSTEM_TEAM;
    }
}
