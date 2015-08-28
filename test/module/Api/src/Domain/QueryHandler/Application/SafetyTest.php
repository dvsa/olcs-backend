<?php

/**
 * Safety Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\Safety;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Transfer\Query\Application\Safety as Qry;

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
        $this->mockRepo('Application', ApplicationRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setLicence($licence);

        $application->shouldReceive('jsonSerialize')
            ->andReturn(['foo' => 'bar']);

        $application->shouldReceive('getGoodsOrPsv->getId')
            ->andReturn(Licence::LICENCE_CATEGORY_GOODS_VEHICLE);

        $query = Qry::create(['id' => 111]);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $this->repoMap['Licence']->shouldReceive('fetchSafetyDetailsUsingId')
            ->with($licence)
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

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setLicence($licence);

        $application->shouldReceive('jsonSerialize')
            ->andReturn(['foo' => 'bar']);

        $application->shouldReceive('getGoodsOrPsv->getId')
            ->andReturn(Licence::LICENCE_CATEGORY_PSV);

        $query = Qry::create(['id' => 111]);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $this->repoMap['Licence']->shouldReceive('fetchSafetyDetailsUsingId')
            ->with($licence)
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
