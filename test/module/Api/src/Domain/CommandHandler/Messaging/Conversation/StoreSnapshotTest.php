<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Messaging\Conversation;

use Dvsa\Olcs\Api\Domain\CommandHandler\Messaging\Conversation\StoreSnapshot as Sut;
use Dvsa\Olcs\Api\Domain\Command\Messaging\Conversation\StoreSnapshot as Cmd;
use Dvsa\Olcs\Api\Domain\Repository\Conversation;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingConversation;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Messaging\Generator;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractCreateSnapshotHandlerTest;
use Mockery as m;

class StoreSnapshotTest extends AbstractCreateSnapshotHandlerTest
{
    protected $cmdClass = Cmd::class;
    protected $sutClass = Sut::class;
    protected $repoServiceName = Conversation::class;
    protected $repoClass = Conversation::class;
    protected $entityClass = MessagingConversation::class;
    protected $documentCategory = Category::CATEGORY_LICENSING;
    protected $documentSubCategory = SubCategory::DOC_SUB_CATEGORY_MESSAGING;
    protected $documentDescription = 'Conversation Snapshot';
    protected $documentLinkId = 'messagingConversation';
    protected $documentLinkValue = null;
    protected $generatorClass = Generator::class;

    protected function extraEntityAssertions(m\MockInterface $entity)
    {

        return $entity;
    }
}
