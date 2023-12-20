<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Messaging;

use ArrayIterator;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Conversations\ByLicence;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\Messaging\Conversations\ByLicence as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use LmcRbacMvc\Service\AuthorizationService;
use Mockery as m;

class ByLicenceTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new ByLicence();
        $this->mockRepo('Conversation', Repository\Conversation::class);
        $this->mockRepo('Message', Repository\Message::class);

        $this->mockedSmServices = ['SectionAccessService' => m::mock(), AuthorizationService::class => m::mock(AuthorizationService::class)->shouldReceive('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(true)->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(false)->getMock(),];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser->getId')->andReturn(1);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([
            'licence' => 1,
        ]);

        $conversations = new ArrayIterator([['id' => 1, 'isClosed' => false,],['id' => 2, 'isClosed' => true,],]);
        $mockQb = m::mock(QueryBuilder::class);
        $this->repoMap['Conversation']->shouldReceive('getBaseConversationListQuery')->andReturn($mockQb);
        $this->repoMap['Conversation']->shouldReceive('filterByLicenceId')->once()->with($mockQb, $query->getLicence())->andReturn($mockQb);
        $this->repoMap['Conversation']->shouldReceive('applyOrderByOpen')->once()->with($mockQb)->andReturn($mockQb);
        $this->repoMap['Conversation']->shouldReceive('fetchPaginatedList')->once()->andReturn($conversations);
        $this->repoMap['Conversation']->shouldReceive('fetchPaginatedCount')->once()->andReturn(0);
        $this->repoMap['Message']->shouldReceive('getUnreadMessagesByConversationIdAndUserId')->andReturn([]);
        $this->repoMap['Message']->shouldReceive('getLastMessageByConversationId')->twice()->andReturn($conversations[0]);

        $result = $this->sut->handleQuery($query);

        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertCount(count($conversations), $result['result']);
    }

    public function testHandleConversationOrdering()
    {
        $query = Qry::create([
            'licence' => 1,
        ]);

        $conversations = new ArrayIterator([$conversation1 = ['id' => 1, 'isClosed' => true,], $conversation2 = ['id' => 2, 'isClosed' => false,], $conversation3 = ['id' => 3, 'isClosed' => false,], $conversation4 = ['id' => 4, 'isClosed' => false,],]);
        $mockQb = m::mock(QueryBuilder::class);
        $this->repoMap['Conversation']->shouldReceive('getBaseConversationListQuery')->andReturn($mockQb);
        $this->repoMap['Conversation']->shouldReceive('filterByLicenceId')->once()->with($mockQb, $query->getLicence())->andReturn($mockQb);
        $this->repoMap['Conversation']->shouldReceive('applyOrderByOpen')->once()->with($mockQb)->andReturn($mockQb);
        $this->repoMap['Conversation']->shouldReceive('fetchPaginatedList')->once()->andReturn($conversations);
        $this->repoMap['Conversation']->shouldReceive('fetchPaginatedCount')->once()->andReturn(0);
        $this->repoMap['Message']->shouldReceive('getLastMessageByConversationId')->once()->with(1)->andReturn(['createdOn' => '2023-11-06T12:17:12+0000']);
        $this->repoMap['Message']->shouldReceive('getLastMessageByConversationId')->once()->with(2)->andReturn(['createdOn' => '2023-11-06T12:52:12+0000']);
        $this->repoMap['Message']->shouldReceive('getLastMessageByConversationId')->once()->with(3)->andReturn(['createdOn' => '2023-11-06T12:10:12+0000']);
        $this->repoMap['Message']->shouldReceive('getLastMessageByConversationId')->once()->with(4)->andReturn(['createdOn' => '2023-11-06T12:30:12+0000']);
        $this->repoMap['Message']->shouldReceive('getUnreadMessagesByConversationIdAndUserId')->once()->with(1, 1)->andReturn([]);
        $this->repoMap['Message']->shouldReceive('getUnreadMessagesByConversationIdAndUserId')->once()->with(2, 1)->andReturn([]);
        $this->repoMap['Message']->shouldReceive('getUnreadMessagesByConversationIdAndUserId')->once()->with(3, 1)->andReturn([['id' => 4, 'createdOn' => '2023-11-06T12:10:12+0000'],]);
        $this->repoMap['Message']->shouldReceive('getUnreadMessagesByConversationIdAndUserId')->once()->with(4, 1)->andReturn([]);

        $result = $this->sut->handleQuery($query);

        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertCount(count($conversations), $result['result']);
        $this->assertEquals($conversation3['id'], $result['result'][0]['id']);
        $this->assertEquals($conversation2['id'], $result['result'][1]['id']);
        $this->assertEquals($conversation4['id'], $result['result'][2]['id']);
        $this->assertEquals($conversation1['id'], $result['result'][3]['id']);
    }
}