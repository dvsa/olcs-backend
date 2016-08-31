<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

/**
 * Can Update TxcInbox record
 */
class CanUpdateTxcInboxRecord extends AbstractDoesOwnEntity
{
    protected $repo = 'User';

    /**
     * Is valid
     *
     * @param int|null $entityId Entity id
     *
     * @return bool
     */
    public function isValid($entityId)
    {
        if ($entityId === null) {
            return false;
        }
        
        if ($this->isLocalAuthority() || $this->isInternalUser()) {
            return true;
        }

        return $this->isGranted(
            \Dvsa\Olcs\Api\Entity\User\Permission::CAN_MANAGE_USER_SELFSERVE,
            ($entityId !== null) ? $this->getEntity($entityId) : null
        );
    }
}
