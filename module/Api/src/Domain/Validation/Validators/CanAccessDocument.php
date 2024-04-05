<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query;

class CanAccessDocument extends AbstractCanAccessEntity
{
    protected $repo = Repository\Document::class;

    /**
     * @throws NotFoundException
     */
    public function isValid($entityId): bool
    {
        if ($this->isTransportManager()) {
            return $this->canTransportManagerAccessDocument($entityId);
        }

        if ($this->isLocalAuthority()) {
            return $this->canLocalAuthorityAccessDocument($entityId);
        }

        if ($this->isExternalUser() && !$this->canExternalUserAccessDocument($entityId)) {
            return false;
        }

        return parent::isValid($entityId);
    }

    private function canLocalAuthorityAccessDocument(int $documentId): bool
    {
        $txcInboxRepo = $this->getRepo(Repository\TxcInbox::class);
        $txcEntities = $txcInboxRepo->fetchLinkedToDocument($documentId);

        if (!is_array($txcEntities) || count($txcEntities) === 0) {
            return false;
        }

        $localAuthorityUser = $this->getCurrentLocalAuthority();

        foreach ($txcEntities as $txcEntity) {
            $txcLocalAuthority = $txcEntity->getLocalAuthority();

            if ($txcLocalAuthority instanceof Entity\Bus\LocalAuthority && $txcLocalAuthority->getId() === $localAuthorityUser->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws NotFoundException
     */
    private function canExternalUserAccessDocument(int $documentId): bool
    {
        return $this->checkDocumentWasExternallyUploaded($documentId)
            || $this->checkDocumentInCorrespondence($documentId)
            || $this->checkIsTxcDocument($documentId);
    }

    private function checkDocumentInCorrespondence(int $documentId): bool
    {
        $currentUserOrganisationId = $this->getCurrentOrganisation() ? $this->getCurrentOrganisation()->getId() : null;
        if ($currentUserOrganisationId === null) {
            return false;
        }

        $query = Query\Correspondence\Correspondences::create([
            'organisation' => $currentUserOrganisationId,
        ]);

        $correspondences = $this->getRepo(Repository\Correspondence::class)->fetchList($query);
        $correspondencesDocumentIds = array_map(function ($element) {
            return $element['document'];
        }, iterator_to_array($correspondences));

        return in_array($documentId, $correspondencesDocumentIds, true);
    }

    /**
     * @throws NotFoundException
     */
    private function checkDocumentWasExternallyUploaded(int $documentId): bool
    {
        $document = $this->getRepo(Repository\Document::class)->fetchById($documentId);
        return $document->getIsExternal();
    }

    private function checkIsTxcDocument(int $documentId): bool
    {
        $txcInboxRepo = $this->getRepo(Repository\TxcInbox::class);
        $txcEntities = $txcInboxRepo->fetchLinkedToDocument($documentId);

        return is_array($txcEntities) && count($txcEntities) > 0;
    }

    /**
     * @throws NotFoundException
     */
    private function canTransportManagerAccessDocument(int $documentId): bool
    {
        $document = $this->getRepo(Repository\Document::class)->fetchById($documentId);
        return $document->getCreatedBy()->getId() === $this->getCurrentUser()->getId();
    }
}
