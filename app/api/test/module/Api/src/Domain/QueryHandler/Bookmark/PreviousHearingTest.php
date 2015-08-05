<?php

/**
 * Previous Hearing Bundle Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Bookmark;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark\PreviousHearing;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\PiHearing as Repo;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\PreviousHearingBundle as Qry;
use Dvsa\Olcs\Api\Entity\Pi\PiHearing as Entity;

/**
 * Previous Hearing Bundle Test
 */
class PreviousHearingTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PreviousHearing();
        $this->mockRepo('PiHearing', Repo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $pi = 99;
        $hearingDate = '2015-03-06 20:00:00';
        $bundle = [];

        $query = Qry::create(['pi' => $pi, 'hearingDate' => $hearingDate, 'bundle' => $bundle]);

        /** @var Entity $entity */
        $entity = m::mock(Entity::class);

        $this->repoMap['PiHearing']->shouldReceive('fetchPreviousHearing')
            ->with($pi, m::type(\DateTime::class))
            ->andReturn($entity);

        $this->assertEquals($entity, $this->sut->handleQuery($query));
    }
}
