<?php

/**
 * Organisation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Organisation;

use Dvsa\Olcs\Api\Domain\QueryHandler\Organisation\Organisation;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Transfer\Query\Organisation\Organisation as Qry;
use Mockery as m;

/**
 * Organisation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Organisation();
        $this->mockRepo('Organisation', OrganisationRepo::class);

        parent::setUp();
    }

    public function testHandleQueryDisqualified()
    {
        $query = Qry::create(['id' => 111]);

        $mockOrganisation = m::mock(\Dvsa\Olcs\Api\Entity\Organisation\Organisation::class)->makePartial();
        $mockOrganisation->shouldReceive('serialize')->andReturn(['foo' => 'bar']);
        $mockOrganisation->shouldReceive('getDisqualifications->count')->andReturn(2);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($mockOrganisation);

        $this->assertEquals(['foo' => 'bar', 'isDisqualified' => true], $this->sut->handleQuery($query)->serialize());
    }

    public function testHandleQueryNotDisqualified()
    {
        $query = Qry::create(['id' => 111]);

        $mockOrganisation = m::mock(\Dvsa\Olcs\Api\Entity\Organisation\Organisation::class)->makePartial();
        $mockOrganisation->shouldReceive('serialize')->andReturn(['foo' => 'bar']);
        $mockOrganisation->shouldReceive('getDisqualifications->count')->andReturn(0);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($mockOrganisation);

        $this->assertEquals(['foo' => 'bar', 'isDisqualified' => false], $this->sut->handleQuery($query)->serialize());
    }
}
