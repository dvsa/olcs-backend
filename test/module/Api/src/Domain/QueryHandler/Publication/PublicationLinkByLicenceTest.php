<?php

/**
 * PublicationLinkByLicence Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Publication;

use Dvsa\Olcs\Api\Domain\QueryHandler\Publication\PublicationLinkByLicence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\PublicationLink as PublicationLinkRepo;
use Dvsa\Olcs\Transfer\Query\Publication\PublicationLinkLicenceList as Qry;
use Mockery as m;

/**
 * PublicationLinkByLicence Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PublicationLinkByLicenceTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PublicationLinkByLicence();
        $this->mockRepo('PublicationLink', PublicationLinkRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $count = 25;
        $query = Qry::create([]);

        $mockResult = m::mock();
        $mockResult->shouldReceive('serialize')->once()->andReturn('foo');

        $this->repoMap['PublicationLink']->shouldReceive('fetchList')
            ->with($query, m::type('integer'))
            ->andReturn([$mockResult]);

        $this->repoMap['PublicationLink']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn($count);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], $count);
        $this->assertEquals($result['result'], ['foo']);
    }
}
