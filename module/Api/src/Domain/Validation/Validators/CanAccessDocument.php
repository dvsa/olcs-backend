<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Messaging\CanAccessCorrelatedDocuments;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Domain\Repository\TxcInbox as TxcInboxRepo;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority;
use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox as TxcInboxEntity;
use Dvsa\Olcs\Transfer\Query\Correspondence\Correspondences;

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
    public function isValid($entityId): bool
    {
        if ($this->isLocalAuthority() && $this->canLocalAuthorityAccessDocument((int)$entityId)) {
            return true;
        }

        // TODO: Verify local authorities dont have correspondence/docs or messaging access (no point checking [& wasting compute] if assumed correctly)
        if (!$this->isLocalAuthority() && $this->isExternalUser() && !$this->canExternalUserAccessDocument((int)$entityId)) {
            return false;
        }

        // Defer to default canAccessEntity checks...
        return parent::isValid($entityId);
    }

    private function canLocalAuthorityAccessDocument(int $documentId): bool
    {
        /**
         * @var TxcInboxRepo $txcInboxRepo
         * @var TxcInboxEntity $txcEntity
         */
        $txcInboxRepo = $this->getRepo('TxcInbox');
        $txcEntities = $txcInboxRepo->fetchLinkedToDocument($documentId);

        if (is_array($txcEntities) && count($txcEntities) > 0) {
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

    private function canExternalUserAccessDocument(int $documentId): bool
    {
        $currentUserOrganisationId = $this->getCurrentOrganisation()->getId();

        if ($this->existsDocumentIdsInCorrespondenceForOrganisation($documentId, $currentUserOrganisationId)) {
            return true;
        }

        if ($this->existsMessagingDocumentIdsForOrganisation($documentId, $currentUserOrganisationId)) {
            return true;
        }

        if ($this->canAccessCorrelatedMessagingDocumentIds($documentId)) {
            return true;
        }

        return false;
    }

    private function existsDocumentIdsInCorrespondenceForOrganisation(int $documentId, int $organisationId): bool
    {
        $query = Correspondences::create([
            'organisation' => $organisationId,
        ]);

        $correspondences = $this->getRepo('Correspondence')->fetchList($query);
        $correspondencesDocumentIds = array_map(function ($element) {
            return $element['document'];
        }, iterator_to_array($correspondences));

        return in_array($documentId, $correspondencesDocumentIds);
    }

    private function existsMessagingDocumentIdsForOrganisation(int $documentId, int $organisationId): bool
    {
        /** @var Entity\Doc\Document $messagingDocument */
        $messagingDocument = $this->getRepo(Repository\Document::class)->fetchById($documentId);
        if ($messagingDocument->getMessagingConversation() && $messagingDocument->getRelatedOrganisation()->getId() === $organisationId)
        {
            return true;
        }

        return false;
    }

    private function canAccessCorrelatedMessagingDocumentIds(int $documentId): bool
    {
        /** @var CanAccessCorrelatedDocuments $handler */
        $handler = $this->getValidatorManager()->get(CanAccessCorrelatedDocuments::class);
        if ($handler->isValid(\Dvsa\Olcs\Transfer\Query\Messaging\Documents::create([
            'correlationId' => ''
        ]))) {
            return true;
        }
    }
}
