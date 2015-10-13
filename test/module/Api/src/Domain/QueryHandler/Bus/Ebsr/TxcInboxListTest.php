<?php

/**
 * TxcInboxList Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Bus;

use Dvsa\Olcs\Api\Domain\QueryHandler\ResultList;
use Dvsa\Olcs\Api\Domain\QueryHandler\Bus\Ebsr\TxcInboxList;
use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox as TxcInboxEntity;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\TxcInbox as TxcInboxRepo;
use Dvsa\Olcs\Transfer\Query\Bus\Ebsr\TxcInboxList as Qry;
use Mockery as m;

/**
 * TxcInboxListTest
 */
class TxcInboxListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new TxcInboxList();
        $this->mockRepo('TxcInbox', TxcInboxRepo::class);

        $this->mockedSmServices = [
            'ZfcRbac\Service\AuthorizationService' => m::mock('ZfcRbac\Service\AuthorizationService')
        ];

        parent::setUp();
    }

    private function getCurrentUser()
    {
        $mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class);
        $mockUser->shouldReceive('getUser')
            ->andReturnSelf();

        $mockUser->shouldReceive('getLocalAuthority')
            ->andReturnNull();

        return $mockUser;
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $this->mockedSmServices['ZfcRbac\Service\AuthorizationService']
            ->shouldReceive('getIdentity')
            ->once()
            ->andReturn($this->getCurrentUser());

        $mockResult = m::mock(TxcInboxEntity::class)->makePartial();

        $this->repoMap['TxcInbox']->shouldReceive('fetchUnreadListForLocalAuthority')
            ->andReturn([$mockResult]);
        $result = $this->sut->handleQuery($query);
        $this->assertCount(2, $result);
        $this->assertEquals(1, $result['count']);
    }
}
