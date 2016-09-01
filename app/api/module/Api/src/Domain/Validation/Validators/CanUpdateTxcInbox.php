<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;
use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox;

/**
 * Can Update TxcInbox record
 */
class CanUpdateTxcInbox extends AbstractCanAccessEntity
{
    protected $repo = 'User';

    /**
     * Is valid
     **
     * @return bool
     */
    public function isValid($entityIds)
    {
        if (empty($entityIds)) {
            return false;
        }

        if ($this->isInternalUser() || $this->isLocalAuthority()) {
            return true;
        }

        return false;
    }
}
