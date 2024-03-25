<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Messaging\Conversation;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCreateSnapshotHandler;
use Dvsa\Olcs\Api\Domain\Repository\Conversation;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Messaging\EnhancedGenerator;

final class StoreEnhancedSnapshot extends AbstractCreateSnapshotHandler
{
    protected $repoServiceName = Conversation::class;
    protected $generatorClass = EnhancedGenerator::class;
    protected $documentCategory = Category::CATEGORY_LICENSING;
    protected $documentSubCategory = SubCategory::DOC_SUB_CATEGORY_LICENCING_OTHER_DOCUMENTS;
    protected $documentDescription = 'Enhanced Conversation Snapshot';
    protected $documentLinkId = 'messagingConversation';

    /**
     * @inheritDoc
     */
    protected function getDocumentDescription($entity): string
    {
        return $this->documentDescription;
    }
}
