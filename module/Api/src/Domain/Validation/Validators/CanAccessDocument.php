<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\RequestAwareInterface;
use Dvsa\Olcs\Api\Domain\RequestAwareTrait;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query;

class CanAccessDocument extends AbstractCanAccessEntity implements RequestAwareInterface
{
    use RequestAwareTrait;

    protected $repo = Repository\Document::class;

    public function isValid($entityId, ?string $correlationId = null): bool
    {
        if ($this->isLocalAuthority() && $this->canLocalAuthorityAccessDocument((int)$entityId)) {
            return true;
        }

        if (!$this->isLocalAuthority() && $this->isExternalUser() && !$this->canExternalUserAccessDocument((int)$entityId)) {
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

    private function canExternalUserAccessDocument(int $documentId): bool
    {
        $currentUserOrganisationId = $this->getCurrentOrganisation()->getId();

        return $this->checkDocumentWasExternallyUploaded($documentId)
            || $this->checkDocumentInCorrespondence($documentId, $currentUserOrganisationId)
            || $this->checkIsTxcDocument($documentId);
    }

    private function checkDocumentInCorrespondence(int $documentId, int $organisationId): bool
    {
        $query = Query\Correspondence\Correspondences::create([
            'organisation' => $organisationId,
        ]);

        $correspondences = $this->getRepo(Repository\Correspondence::class)->fetchList($query);
        $correspondencesDocumentIds = array_map(function ($element) {
            return $element['document'];
        }, iterator_to_array($correspondences));

        return in_array($documentId, $correspondencesDocumentIds, true);
    }

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
}
