<?php

/**
 * Application Operating Centre Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\ApplicationOperatingCentre;

use Dvsa\Olcs\Api\Entity\Application\Application;
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

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->shouldReceive('isPsv')
            ->andReturn(true)
            ->shouldReceive('isNew')
            ->andReturn(true)
            ->shouldReceive('isVariation')
            ->andReturn(false);

        /** @var ApplicationOperatingCentreEntity $aoc */
        $aoc = m::mock(ApplicationOperatingCentreEntity::class)->makePartial();
        $aoc->setApplication($application);

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
            'wouldIncreaseRequireAdditionalAdvertisement' => false
        ];

        $this->assertEquals($expected, $result->serialize());
    }
}
