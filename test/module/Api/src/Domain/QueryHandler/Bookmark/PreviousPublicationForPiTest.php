<?php

/**
 * Previous Publication For Pi Bundle Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Bookmark;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark\PreviousPublicationForPi;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\PublicationLink as Repo;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\PreviousHearingBundle as Qry;
use Dvsa\Olcs\Api\Entity\Pi\PiHearing as Entity;

/**
 * Previous Publication For Pi Bundle Test
 */
class PreviousPublicationForPiTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PreviousPublicationForPi();
        $this->mockRepo('PublicationLink', Repo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $trafficArea = 99;
        $pi = 88;
        $pubType = 'A&D';
        $publicationNo = 77;
        $bundle = [];

        $query = Qry::create(
            [
                'pi' => $pi,
                'pubType' => $pubType,
                'publicationNo' => $publicationNo,
                'trafficArea' => $trafficArea,
                'bundle' => $bundle
            ]
        );

        /** @var Entity $entity */
        $entity = m::mock(Entity::class);

        $this->repoMap['PublicationLink']->shouldReceive('fetchPreviousPublicationNo')
            ->with($query)
            ->andReturn($entity);

        $this->assertEquals($entity, $this->sut->handleQuery($query));
    }
}
