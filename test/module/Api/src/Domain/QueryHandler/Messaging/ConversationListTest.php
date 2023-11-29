<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Messaging;

use ArrayIterator;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\ConversationList;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\Messaging\GetConversationList as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use LmcRbacMvc\Service\AuthorizationService;

class ConversationListTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new ConversationList();
        $this->mockRepo('Conversation', Repository\Conversation::class);
        $this->mockRepo('Message', Repository\Message::class);

        $this->mockedSmServices = ['SectionAccessService' => m::mock(), AuthorizationService::class => m::mock(AuthorizationService::class)->shouldReceive('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(true)->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(false)->getMock(),];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser->getId')->andReturn(1);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $conversations = new ArrayIterator([['id' => 1, 'isClosed' => false,],]);
        $this->repoMap['Conversation']->shouldReceive('getBaseConversationListQuery')->andReturn(m::mock(QueryBuilder::class));
        $this->repoMap['Conversation']->shouldReceive('applyOrderByOpen')->once();
        $this->repoMap['Conversation']->shouldReceive('fetchPaginatedList')->once()->andReturn($conversations);
        $this->repoMap['Conversation']->shouldReceive('fetchPaginatedCount')->once()->andReturn(0);
        $this->repoMap['Message']->shouldReceive('getUnreadMessagesByConversationIdAndUserId')->andReturn([]);
        $this->repoMap['Message']->shouldReceive('getLastMessageByConversationId')->once()->andReturn($conversations[0]);

        $result = $this->sut->handleQuery($query);

        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('count', $result);
    }

    public function testHandleQueryWithNoOrdering()
    {
        $query = Qry::create(['applyOpenMessageSorting' => false, 'applyNewMessageSorting' => false,]);

        $conversations = new ArrayIterator([['id' => 1, 'isClosed' => true,], ['id' => 2, 'isClosed' => false,],]);
        $this->repoMap['Conversation']->shouldReceive('getBaseConversationListQuery')->andReturn(m::mock(QueryBuilder::class));
        $this->repoMap['Conversation']->shouldReceive('applyOrderByOpen')->never();
        $this->repoMap['Conversation']->shouldReceive('fetchPaginatedList')->once()->andReturn($conversations);
        $this->repoMap['Conversation']->shouldReceive('fetchPaginatedCount')->once()->andReturn(0);
        $this->repoMap['Conversation']->shouldReceive('getLatestMessageMetadata')->with(1)->andReturn(['createdOn' => '2023-11-06T12:16:12+0000']);
        $this->repoMap['Conversation']->shouldReceive('getLatestMessageMetadata')->with(2)->andReturn(['createdOn' => '2023-11-06T12:17:12+0000']);
        $this->repoMap['Message']->shouldReceive('getUnreadMessagesByConversationIdAndUserId')->andReturn([]);
        $this->repoMap['Message']->shouldReceive('getLastMessageByConversationId')->times(2)->andReturn($conversations[0], $conversations[1]);

        $result = $this->sut->handleQuery($query);

        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertEquals(1, $result['result'][0]['id']);
        $this->assertEquals(2, $result['result'][1]['id']);
    }

    public function testHandleQueryWithMessageStatusOrdering()
    {
        $query = Qry::create(['applyOpenMessageSorting' => false, 'applyNewMessageSorting' => true,]);

        $conversations = new ArrayIterator([['id' => 1, 'isClosed' => true,], ['id' => 2, 'isClosed' => false,],['id' => 3, 'isClosed' => false,], ['id' => 4, 'isClosed' => false,],]);
        $mockQb = m::mock(QueryBuilder::class);
        $this->repoMap['Conversation']->shouldReceive('getBaseConversationListQuery')->andReturn($mockQb);
        $this->repoMap['Conversation']->shouldReceive('applyOrderByOpen')->never()->with($mockQb);
        $this->repoMap['Conversation']->shouldReceive('fetchPaginatedList')->once()->andReturn($conversations);
        $this->repoMap['Conversation']->shouldReceive('fetchPaginatedCount')->once()->andReturn(0);
        $this->repoMap['Message']->shouldReceive('getLastMessageByConversationId')->once()->with(1)->andReturn(['createdOn' => '2023-11-06T12:17:12+0000']);
        $this->repoMap['Message']->shouldReceive('getLastMessageByConversationId')->once()->with(2)->andReturn(['createdOn' => '2023-11-06T12:17:12+0000']);
        $this->repoMap['Message']->shouldReceive('getLastMessageByConversationId')->once()->with(3)->andReturn(['createdOn' => '2023-11-06T12:17:12+0000']);
        $this->repoMap['Message']->shouldReceive('getLastMessageByConversationId')->once()->with(4)->andReturn(['createdOn' => '2023-11-06T12:10:12+0000']);
        $this->repoMap['Message']->shouldReceive('getUnreadMessagesByConversationIdAndUserId')->once()->with(1, 1)->andReturn([]);
        $this->repoMap['Message']->shouldReceive('getUnreadMessagesByConversationIdAndUserId')->once()->with(2, 1)->andReturn([['id' => 3, 'createdOn' => '2023-11-06T12:17:12+0000'],]);
        $this->repoMap['Message']->shouldReceive('getUnreadMessagesByConversationIdAndUserId')->once()->with(3, 1)->andReturn([['id' => 4, 'createdOn' => '2023-11-06T12:17:12+0000'],]);
        $this->repoMap['Message']->shouldReceive('getUnreadMessagesByConversationIdAndUserId')->once()->with(4, 1)->andReturn([]);

        $result = $this->sut->handleQuery($query);

        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertEquals(2, $result['result'][0]['id']);
        $this->assertEquals(3, $result['result'][1]['id']);
        $this->assertEquals(4, $result['result'][2]['id']);
        $this->assertEquals(1, $result['result'][3]['id']);
    }
}
