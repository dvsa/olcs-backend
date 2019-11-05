<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Repository\TxcInbox as TxcInboxRepo;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority;
use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox as TxcInboxEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

/**
 * Can Access a Document
 */
class CanAccessDocument extends AbstractCanAccessEntity
{
    protected $repo = 'Document';

    /**
     * Is Valid
     *
     * @param int $entityId Document ID
     *
     * @return bool
     */
    public function isValid($entityId)
    {
        // check if can validate through parent
        $valid = $this->callParentIsValid($entityId);
        if ($valid) {
            // if passed validation then return
            return true;
        }

        /**
         * @todo olcs-14494 emergency fix, need to clean this up
         * attempt to find if the document is linked through a txc_inbox
         *
         * @var TxcInboxRepo $txcInboxRepo
         * @var TxcInboxEntity $txcEntity
         */
        $txcInboxRepo = $this->getRepo('TxcInbox');
        $txcEntities = $txcInboxRepo->fetchLinkedToDocument($entityId);

        if (!empty($txcEntities)) {
            $localAuthorityUser = $this->getCurrentLocalAuthority();

            foreach ($txcEntities as $txcEntity) {
                $txcLocalAuthority = $txcEntity->getLocalAuthority();

                if ($txcLocalAuthority instanceof LocalAuthority && $txcLocalAuthority == $localAuthorityUser) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Call parent isValid method (to facilitate unit testing)
     *
     * @param int $entityId Document ID
     *
     * @return bool
     */
    protected function callParentIsValid($entityId)
    {
        return parent::isValid($entityId);
    }
}
