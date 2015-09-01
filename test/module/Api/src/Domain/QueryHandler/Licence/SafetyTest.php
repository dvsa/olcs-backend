<?php

/**
 * Safety Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\Safety;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Transfer\Query\Licence\Safety as Qry;

/**
 * Safety Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SafetyTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Safety();
        $this->mockRepo('Licence', LicenceRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        $licence->shouldReceive('jsonSerialize')
            ->andReturn(['foo' => 'bar']);

        $licence->shouldReceive('getGoodsOrPsv->getId')
            ->andReturn(Licence::LICENCE_CATEGORY_GOODS_VEHICLE);

        $query = Qry::create(['id' => 111]);

        $this->repoMap['Licence']->shouldReceive('fetchSafetyDetailsUsingId')
            ->with($query)
            ->andReturn($licence);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'canHaveTrailers' => true,
                'hasTrailers' => false
            ],
            $this->sut->handleQuery($query)
        );
    }

    public function testHandleQueryPsv()
    {
        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        $licence->shouldReceive('jsonSerialize')
            ->andReturn(['foo' => 'bar']);

        $licence->shouldReceive('getGoodsOrPsv->getId')
            ->andReturn(Licence::LICENCE_CATEGORY_PSV);

        $query = Qry::create(['id' => 111]);

        $this->repoMap['Licence']->shouldReceive('fetchSafetyDetailsUsingId')
            ->with($query)
            ->andReturn($licence);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'canHaveTrailers' => false,
                'hasTrailers' => false
            ],
            $this->sut->handleQuery($query)
        );
    }
}
