<?php

/**
 * Next Item Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Queue;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\Queue\NextItem;
use Dvsa\Olcs\Api\Domain\Repository\Queue as QueueRepo;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Domain\Query\Queue\NextItem as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * Next Item Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class NextItemTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new NextItem();
        $this->mockRepo('Queue', QueueRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $typeId = 'foo';
        $item = m::mock(QueueEntity::class)->makePartial();

        $query = Qry::create(['type' => $typeId]);

        $this->repoMap['Queue']
            ->shouldReceive('getNextItem')
            ->with($typeId)
            ->once()
            ->andReturn($item);

        $this->assertSame($item, $this->sut->handleQuery($query));
    }
}
