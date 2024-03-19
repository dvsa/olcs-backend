<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Messaging\Conversations;

use ArrayIterator;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Conversations\ByOrganisation;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\Messaging\Conversations\ByOrganisation as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use LmcRbacMvc\Service\AuthorizationService;
use Mockery as m;

class ByOrganisationTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new ByOrganisation();
        $this->mockRepo(Repository\Conversation::class, Repository\Conversation::class);
        $this->mockRepo(Repository\Message::class, Repository\Message::class);

        $this->mockedSmServices = [
            'SectionAccessService' => m::mock(),
            AuthorizationService::class => m::mock(AuthorizationService::class)
                ->shouldReceive('isGranted')
                ->with(Permission::SELFSERVE_USER, null)
                ->andReturn(true)
                ->shouldReceive('isGranted')
                ->with(Permission::INTERNAL_USER, null)
                ->andReturn(false)
                ->getMock(),
        ];

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('getIdentity->getUser->getId')
            ->andReturn(1);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([
            'organisation' => 1,
        ]);

        $conversations = new ArrayIterator([['id' => 1, 'isClosed' => false,], ['id' => 2, 'isClosed' => true,],]);
        $mockQb = m::mock(QueryBuilder::class);
        $this->repoMap[Repository\Conversation::class]->shouldReceive('getByOrganisationId')->once()->andReturn($mockQb);
        $this->repoMap[Repository\Conversation::class]->shouldReceive('filterByStatuses')->once()->with($mockQb, [])->andReturn($mockQb);
        $this->repoMap[Repository\Conversation::class]->shouldReceive('fetchPaginatedList')->once()->andReturn($conversations);
        $this->repoMap[Repository\Conversation::class]->shouldReceive('fetchPaginatedCount')->once()->andReturn(10);
        $this->repoMap[Repository\Message::class]->shouldReceive('getUnreadMessagesByConversationIdAndUserId')->andReturn([]);
        $this->repoMap[Repository\Message::class]->shouldReceive('getLastMessageByConversationId')->twice()->andReturn($conversations[0]);

        $result = $this->sut->handleQuery($query);

        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertCount(2, $result['result']);
        $this->assertEquals(10, $result['count']);
    }

    public function testHandleConversationOrdering()
    {
        $query = Qry::create([
            'organisation' => 1,
        ]);

        $conversations = new ArrayIterator([$conversation1 = ['id' => 1, 'isClosed' => true,], $conversation2 = ['id' => 2, 'isClosed' => false,], $conversation3 = ['id' => 3, 'isClosed' => false,], $conversation4 = ['id' => 4, 'isClosed' => false,],]);
        $mockQb = m::mock(QueryBuilder::class);
        $this->repoMap[Repository\Conversation::class]->shouldReceive('getByOrganisationId')->once()->andReturn($mockQb);
        $this->repoMap[Repository\Conversation::class]->shouldReceive('filterByStatuses')->once()->with($mockQb, [])->andReturn($mockQb);
        $this->repoMap[Repository\Conversation::class]->shouldReceive('fetchPaginatedList')->once()->andReturn($conversations);
        $this->repoMap[Repository\Conversation::class]->shouldReceive('fetchPaginatedCount')->once()->andReturn(0);
        $this->repoMap[Repository\Message::class]->shouldReceive('getLastMessageByConversationId')->once()->with(1)->andReturn(['createdOn' => '2023-11-06T12:17:12+0000']);
        $this->repoMap[Repository\Message::class]->shouldReceive('getLastMessageByConversationId')->once()->with(2)->andReturn(['createdOn' => '2023-11-06T12:52:12+0000']);
        $this->repoMap[Repository\Message::class]->shouldReceive('getLastMessageByConversationId')->once()->with(3)->andReturn(['createdOn' => '2023-11-06T12:10:12+0000']);
        $this->repoMap[Repository\Message::class]->shouldReceive('getLastMessageByConversationId')->once()->with(4)->andReturn(['createdOn' => '2023-11-06T12:30:12+0000']);
        $this->repoMap[Repository\Message::class]->shouldReceive('getUnreadMessagesByConversationIdAndUserId')->once()->with(1, 1)->andReturn([]);
        $this->repoMap[Repository\Message::class]->shouldReceive('getUnreadMessagesByConversationIdAndUserId')->once()->with(2, 1)->andReturn([]);
        $this->repoMap[Repository\Message::class]->shouldReceive('getUnreadMessagesByConversationIdAndUserId')->once()->with(3, 1)->andReturn([['id' => 4, 'createdOn' => '2023-11-06T12:10:12+0000'],]);
        $this->repoMap[Repository\Message::class]->shouldReceive('getUnreadMessagesByConversationIdAndUserId')->once()->with(4, 1)->andReturn([]);

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
