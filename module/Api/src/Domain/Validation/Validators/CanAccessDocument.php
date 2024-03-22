<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Repository\TxcInbox as TxcInboxRepo;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority;
use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox as TxcInboxEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Transfer\Query\Correspondence\Correspondences;

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
        // If local authority user, check if document linked through a txc_inbox
        if ($this->isLocalAuthority()) {
            /**
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
        }

        // If external user, check if requested document is available in documents/correspondence tab on dashboard.
        if ($this->isExternalUser()) {
            $query = Correspondences::create([
                'organisation' => $this->getCurrentOrganisation()->getId(),
            ]);
            $correspondences = $this->getRepo('Correspondence')->fetchList($query);
            $correspondencesDocumentIds = array_map(function ($element) {
                return $element['document'];
            }, $correspondences);
            if (!in_array($entityId, $correspondencesDocumentIds)) {
                return false;
            }
        }

        // Defer to default canAccessEntity checks...
        return parent::isValid($entityId);
    }
}
