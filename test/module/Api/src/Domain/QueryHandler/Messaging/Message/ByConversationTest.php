<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Messaging\Message;

use ArrayIterator;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Message\ByConversation;
use Dvsa\Olcs\Api\Domain\Repository;
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
        $this->mockRepo('Conversation', Repository\Conversation::class);
        $this->mockRepo('Message', Repository\Message::class);

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
        $conversation = [
            'id' => 1,
            'isDisabled' => false,
        ];
        $mockQb = m::mock(QueryBuilder::class);
        $this->repoMap['Message']->shouldReceive('getBaseMessageListWithContentQuery')->andReturn($mockQb);
        $this->repoMap['Message']->shouldReceive('filterByConversationId')->andReturn($mockQb);
        $this->repoMap['Message']->shouldReceive('fetchPaginatedList')->with($mockQb)->once()->andReturn($messages);
        $this->repoMap['Message']->shouldReceive('fetchPaginatedCount')->with($mockQb)->once()->andReturn(10);
        $this->repoMap['Conversation']->shouldReceive('fetchById')->with(1)->once()->andReturn($conversation);

        $result = $this->sut->handleQuery($query);

        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertCount(8, $result['result']);
        $this->assertEquals(10, $result['count']);
    }
}
