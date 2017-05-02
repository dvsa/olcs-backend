<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\ApplicationOperatingCentre;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\ApplicationOperatingCentre\ApplicationOperatingCentre;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\ApplicationOperatingCentre\ApplicationOperatingCentre as Qry;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre as ApplicationOperatingCentreEntity;
use Dvsa\Olcs\Api\Domain\Repository;

/**
 * Application Operating Centre Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationOperatingCentreTest extends QueryHandlerTestCase
{
    protected $expectedBundle = [
        'operatingCentre' => [
            'address' => [
                'countryCode'
            ],
            'adDocuments'
        ]
    ];

    public function setUp()
    {
        $this->sut = new ApplicationOperatingCentre();
        $this->mockRepo('ApplicationOperatingCentre', Repository\ApplicationOperatingCentre::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setLicence($licence);
        $application->shouldReceive('isPsv')
            ->andReturn(true)
            ->shouldReceive('isNew')
            ->andReturn(true)
            ->shouldReceive('isVariation')
            ->andReturn(false)
            ->shouldReceive('getNiFlag')
            ->andReturn('Y')
            ->shouldReceive('getAppliedVia->getId')
            ->andReturn('foo')
            ->once()
            ->getMock();

        /** @var ApplicationOperatingCentreEntity $aoc */
        $aoc = m::mock(ApplicationOperatingCentreEntity::class)->makePartial();
        $aoc->setApplication($application);
        $aoc->setAction('D');

        $aoc->shouldReceive('serialize')
            ->with($this->expectedBundle)
            ->andReturn(['foo' => 'bar']);

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($aoc);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'isPsv' => true,
            'canUpdateAddress' => true,
            'wouldIncreaseRequireAdditionalAdvertisement' => false,
            'currentVehiclesRequired' => null,
            'currentTrailersRequired' => null,
            'niFlag' => 'Y',
            'appliedVia' => 'foo'
        ];

        $this->assertEquals($expected, $result->serialize());
    }

    public function testHandleQueryWithUpdated()
    {
        $query = Qry::create(['id' => 111]);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setLicence($licence);
        $application->shouldReceive('isPsv')
            ->andReturn(true)
            ->shouldReceive('isNew')
            ->andReturn(true)
            ->shouldReceive('isVariation')
            ->andReturn(false)
            ->shouldReceive('getNiFlag')
            ->andReturn('Y')
            ->shouldReceive('getAppliedVia->getId')
            ->andReturn('foo')
            ->once()
            ->getMock();

        /** @var ApplicationOperatingCentreEntity $aoc */
        $aoc = m::mock(ApplicationOperatingCentreEntity::class)->makePartial();
        $aoc->setApplication($application);
        $aoc->setAction('U');

        $aoc->shouldReceive('serialize')
            ->with($this->expectedBundle)
            ->andReturn(['foo' => 'bar']);

        /** @var LicenceOperatingCentre $loc */
        $loc = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc->setNoOfVehiclesRequired(10);
        $loc->setNoOfTrailersRequired(9);

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($aoc)
            ->shouldReceive('findCorrespondingLoc')
            ->with($aoc, $licence)
            ->andReturn($loc);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'isPsv' => true,
            'canUpdateAddress' => true,
            'wouldIncreaseRequireAdditionalAdvertisement' => false,
            'currentVehiclesRequired' => 10,
            'currentTrailersRequired' => 9,
            'niFlag' => 'Y',
            'appliedVia' => 'foo'
        ];

        $this->assertEquals($expected, $result->serialize());
    }
}
