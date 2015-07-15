<?php

/**
 * Operating Centres Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\InspectionRequest;

use Dvsa\Olcs\Api\Domain\QueryHandler\InspectionRequest\OperatingCentres as QueryHandler;
use Dvsa\Olcs\Transfer\Query\InspectionRequest\OperatingCentres as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\InspectionRequest as InspectionRequestRepo;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;

/**
 * Operating Centres Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OperatingCentresTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('InspectionRequest', InspectionRequestRepo::class);
        $this->mockRepo('Application', ApplicationRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);

        parent::setUp();
    }

    public function testHandleQueryForLicence()
    {
        $query = Query::create(['type' => 'licence', 'identifier' => 1]);

        $mockLicence = m::mock()
            ->shouldReceive('getOcForInspectionRequest')
            ->andReturn(['foo'])
            ->once()
            ->getMock();

        $this->repoMap['Licence']
            ->shouldReceive('fetchWithOperatingCentres')
            ->with(1)
            ->andReturn($mockLicence)
            ->once()
            ->getMock();

        $this->assertEquals(['result' => ['foo'], 'count' => 1], $this->sut->handleQuery($query));
    }

    public function testHandleQueryForApplication()
    {
        $query = Query::create(['type' => 'application', 'identifier' => 1]);

        $mockLicence = m::mock()
            ->shouldReceive('getOcForInspectionRequest')
            ->andReturn(['foo'])
            ->once()
            ->getMock();

        $this->repoMap['Application']
            ->shouldReceive('fetchWithLicenceAndOc')
            ->with(1)
            ->andReturn($mockLicence)
            ->once()
            ->getMock();

        $this->assertEquals(['result' => ['foo'], 'count' => 1], $this->sut->handleQuery($query));
    }
}
