<?php

/**
 * PiVenue Bundle Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Bookmark;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark\PiVenueBundle;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\PiVenue as Repo;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\PiVenueBundle as Qry;
use Dvsa\Olcs\Api\Entity\Pi\PiVenue as Entity;

/**
 * PiVenue Bundle Test
 */
class PiVenueBundleTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PiVenueBundle();
        $this->mockRepo('PiVenue', Repo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $id = 99;
        $bundle = [];

        $query = Qry::create(['id' => $id, 'bundle' => $bundle]);

        /** @var Entity $entity */
        $entity = m::mock(Entity::class);

        $this->repoMap['PiVenue']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($entity);

        $this->assertEquals($entity, $this->sut->handleQuery($query));
    }
}
