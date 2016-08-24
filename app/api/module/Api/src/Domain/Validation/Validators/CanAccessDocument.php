<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

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
        $valid = parent::isValid($entityId);
        if ($valid) {
            // if passed validation then return
            return true;
        }

        // attempt to find if the document is linked through a txc_inbox
        $entities = $this->getRepo('TxcInbox')->fetchLinkedToDocument($entityId);
        if (!empty($entities)) {
            return $this->isOwner($entities[0]);
        }

        return false;
    }
}
