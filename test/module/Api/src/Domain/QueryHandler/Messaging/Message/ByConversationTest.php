<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Messaging\Message;

use ArrayIterator;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Message\ByConversation;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingUserMessageRead;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingConversation;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\Messaging\Messages\ByConversation as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use LmcRbacMvc\Service\AuthorizationService;
use Mockery as m;

class ByConversationTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new ByConversation();
        $this->mockRepo(Repository\Conversation::class, Repository\Conversation::class);
        $this->mockRepo(Repository\Message::class, Repository\Message::class);
        $this->mockRepo(Repository\MessagingUserMessageRead::class, Repository\MessagingUserMessageRead::class);

        $this->mockedSmServices = ['SectionAccessService' => m::mock(), AuthorizationService::class => m::mock(AuthorizationService::class)->shouldReceive('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(true)->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(false)->getMock(),];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser->getId')->andReturn(1);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([
            'conversation' => 1,
        ]);

        $messages = new ArrayIterator(
            [
                ['id' => 1,'messaging_conversation_id' => '1','messaging_content_id' => '1'],
                ['id' => 2,'messaging_conversation_id' => '1','messaging_content_id' => '2'],
                ['id' => 3,'messaging_conversation_id' => '1','messaging_content_id' => '3'],
                ['id' => 4,'messaging_conversation_id' => '1','messaging_content_id' => '4'],
                ['id' => 5,'messaging_conversation_id' => '1','messaging_content_id' => '5'],
                ['id' => 6,'messaging_conversation_id' => '1','messaging_content_id' => '6'],
                ['id' => 7,'messaging_conversation_id' => '1','messaging_content_id' => '7'],
                ['id' => 8,'messaging_conversation_id' => '1','messaging_content_id' => '8'],
            ]
        );
        $conversation = new MessagingConversation();
        $mockQb = m::mock(QueryBuilder::class);
        $this->repoMap[Repository\Message::class]->shouldReceive('getBaseMessageListWithContentQuery')->andReturn($mockQb);
        $this->repoMap[Repository\Message::class]->shouldReceive('filterByConversationId')->andReturn($mockQb);
        $this->repoMap[Repository\Message::class]->shouldReceive('fetchPaginatedList')->with($mockQb)->once()->andReturn($messages);
        $this->repoMap[Repository\Message::class]->shouldReceive('fetchPaginatedCount')->with($mockQb)->once()->andReturn(10);
        $this->repoMap[Repository\Conversation::class]->shouldReceive('fetchById')->with(1)->once()->andReturn($conversation);

        foreach ($messages as $message) {
            $this->repoMap[Repository\Message::class]
                ->shouldReceive('fetchById')
                ->with($message['id'])
                ->once()
                ->andReturn($message);
        }

        $mockUserMessageRead = m::mock(MessagingUserMessageRead::class);
        $mockUserMessageRead->shouldReceive('setLastReadOn')->times(count($messages));
        $this->repoMap[Repository\MessagingUserMessageRead::class]->shouldReceive('fetchByMessageIdAndUserId')->times(count($messages))->andReturn($mockUserMessageRead);
        $this->repoMap[Repository\MessagingUserMessageRead::class]->shouldReceive('save')->times(count($messages));

        $result = $this->sut->handleQuery($query);

        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertCount(8, $result['result']);
        $this->assertEquals(10, $result['count']);
    }
}
