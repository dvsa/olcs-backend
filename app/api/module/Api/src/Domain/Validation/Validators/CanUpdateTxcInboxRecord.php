<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;
use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox;

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

        if ($this->isInternalUser()) {
            return true;
        }

        if ($this->isLocalAuthority()) {
            // check the local authority matches
            $entity = $this->getEntity($entityId);

            if ($entity instanceOf TxcInbox && $entity->getLocalAuthority()->getId() ===
                $this->getCurrentUser()->getLocalAuthority()->getId()) {
                return true;
            }
        }

        return false;
    }
}
