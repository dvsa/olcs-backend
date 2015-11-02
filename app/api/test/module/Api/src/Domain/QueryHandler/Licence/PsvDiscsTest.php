<?php

/**
 * PsvDiscs Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\PsvDiscs;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Transfer\Query\Licence\PsvDiscs as Qry;

/**
 * PsvDiscs Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvDiscsTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PsvDiscs();
        $this->mockRepo('Licence', LicenceRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111, 'includeCeased' => true]);

        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('serialize')
            ->once()
            ->with(['psvDiscs'])
            ->andReturn(['foo' => 'bar', 'remainingSpacesPsv' => 10])
            ->shouldReceive('getRemainingSpacesPsv')
            ->andReturn(10)
            ->once();

        $licence->shouldReceive('getPsvDiscs->count')
            ->andReturn(10);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($licence);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals(['foo' => 'bar', 'remainingSpacesPsv' => 10, 'totalPsvDiscs' => 10], $result->serialize());
    }

    public function testHandleQueryWithoutCeased()
    {
        $query = Qry::create(['id' => 111, 'includeCeased' => false]);

        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('serialize')
            ->once()
            ->andReturnUsing(
                function ($bundle) {

                    $this->assertInstanceOf(Criteria::class, $bundle['psvDiscs']['criteria']);

                    return ['foo' => 'bar'];
                }
            )
            ->shouldReceive('getRemainingSpacesPsv')
            ->andReturn(10)
            ->once();

        $licence->shouldReceive('getPsvDiscs->count')
            ->andReturn(10);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($licence);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals(['foo' => 'bar', 'remainingSpacesPsv' => 10, 'totalPsvDiscs' => 10], $result->serialize());
    }
}
