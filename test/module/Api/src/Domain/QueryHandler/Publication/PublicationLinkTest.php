<?php

/**
 * PublicationLink Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Publication;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Publication\PublicationLink;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\PublicationLink as PublicationLinkRepo;
use Dvsa\Olcs\Transfer\Query\Publication\PublicationLink as Qry;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as PublicationLinkEntity;

/**
 * PublicationLink Test
 */
class PublicationLinkTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PublicationLink();
        $this->mockRepo('PublicationLink', PublicationLinkRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);
        $isNew = true;
        $output = [
            'isNew' => $isNew
        ];

        $publicationLink = m::mock(PublicationLinkEntity::class)
            ->shouldReceive('serialize')
            ->andReturn($output)
            ->getMock();
        $publicationLink->shouldReceive('getPublication->isNew')->andReturn($isNew);

        $this->repoMap['PublicationLink']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($publicationLink);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($output, $result->serialize());
    }
}
