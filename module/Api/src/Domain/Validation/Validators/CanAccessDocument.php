<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\RequestAwareInterface;
use Dvsa\Olcs\Api\Domain\RequestAwareTrait;
use Dvsa\Olcs\Api\Domain\Validation\Validators\CanAccessCorrelatedDocument;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Domain\Repository\TxcInbox as TxcInboxRepo;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority;
use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox as TxcInboxEntity;
use Dvsa\Olcs\Transfer\Query\Correspondence\Correspondences;
use Dvsa\Olcs\Transfer\Query\Messaging\Documents;

class CanAccessDocument extends AbstractCanAccessEntity implements RequestAwareInterface
{
    use RequestAwareTrait;

    protected $repo = 'Document';

    /**
     * Is Valid
     *
     * @param int $entityId Document ID
     * @param string|null $correlationId
     * @return bool
     */
    public function isValid($entityId, string $correlationId = null): bool
    {
        if ($this->isLocalAuthority() && $this->canLocalAuthorityAccessDocument((int)$entityId)) {
            return true;
        }

        if (!$this->isLocalAuthority() && $this->isExternalUser() && !$this->canExternalUserAccessDocument((int)$entityId, $correlationId)) {
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

                if ($txcLocalAuthority instanceof LocalAuthority && $txcLocalAuthority->getId() === $localAuthorityUser->getId()) {
                    return true;
                }
            }
        }

        return false;
    }

    private function canExternalUserAccessDocument(int $documentId, string $correlationId = null): bool
    {
        $currentUserOrganisationId = $this->getCurrentOrganisation()->getId();

        return $this->checkDocumentWasExternallyUploaded($documentId)
            || $this->checkDocumentInCorrespondence($documentId, $currentUserOrganisationId)
            || $this->checkIsTxcDocument($documentId);
    }

    private function checkDocumentInCorrespondence(int $documentId, int $organisationId): bool
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

    private function checkDocumentWasExternallyUploaded(int $documentId): bool
    {
        /** @var Entity\Doc\Document $document */
        $document = $this->getRepo('Document')->fetchById($documentId);
        return $document->getIsExternal();
    }

    private function checkIsTxcDocument(int $documentId): bool
    {
        /**
         * @var TxcInboxRepo $txcInboxRepo
         * @var TxcInboxEntity $txcEntity
         */
        $txcInboxRepo = $this->getRepo('TxcInbox');
        $txcEntities = $txcInboxRepo->fetchLinkedToDocument($documentId);

        return is_array($txcEntities) && count($txcEntities) > 0;
    }
}
