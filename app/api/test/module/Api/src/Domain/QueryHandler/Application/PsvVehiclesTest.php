<?php

/**
 * Psv Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\PsvVehicles;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\Service\PsvVehicles\PsvVehiclesQueryHelper;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query\Application\PsvVehicles as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use Doctrine\ORM\Query;

/**
 * Psv Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvVehiclesTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PsvVehicles();
        $this->mockRepo('Application', Repository\Application::class);
        $this->mockRepo('LicenceVehicle', Repository\LicenceVehicle::class);
        $this->mockedSmServices['PsvVehiclesQueryHelper'] = m::mock(PsvVehiclesQueryHelper::class)->makePartial();

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(
            [
                'id' => 111,
                'includeRemoved' => true
            ]
        );

        $mockQb = m::mock(\Doctrine\ORM\QueryBuilder::class);
        /** @var Entity\Application\Application|m\MockInterface $application */
        $application = m::mock(Entity\Application\Application::class);
        $application->shouldReceive('serialize')
            ->with([])
            ->andReturn(['foo' => 'bar'])
            ->shouldReceive('getId')
            ->andReturn(111)
            ->once()
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(222)
                ->once()
                ->getMock()
            )
            ->once()
            ->shouldReceive('getTotAuthVehicles')
            ->andReturn(0)
            ->once()
            ->shouldReceive('getActiveVehiclesCount')
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $mockList = m::mock()
            ->shouldReceive('serialize')
            ->andReturn(['foo' => 'bar'])
            ->once()
            ->getMock();

        $this->repoMap['LicenceVehicle']
            ->shouldReceive('createPaginatedVehiclesDataForApplicationQueryPsv')
            ->with($query, 111, 222)
            ->andReturn($mockQb)
            ->once()
            ->shouldReceive('fetchPaginatedList')
            ->with($mockQb, Query::HYDRATE_OBJECT)
            ->andReturn([$mockList])
            ->once()
            ->shouldReceive('fetchPaginatedCount')
            ->andReturn(1)
            ->with($mockQb)
            ->once()
            ->shouldReceive('fetchAllVehiclesCount')
            ->with(222)
            ->andReturn(3)
            ->once()
            ->getMock();

        $flags = [
            'showSmallTable' => true,
            'showMediumTable' => true,
            'showLargeTable' => true,
            'smallAuthExceeded' => true,
            'mediumAuthExceeded' => true,
            'largeAuthExceeded' => true,
            'availableSmallSpaces' => 9,
            'availableMediumSpaces' => 8,
            'availableLargeSpaces' => 7,
            'small' => [
                ['type' => 'small']
            ],
            'medium' => [
                ['type' => 'medium']
            ],
            'large' => [
                ['type' => 'large']
            ],
        ];

        $this->mockedSmServices['PsvVehiclesQueryHelper']->shouldReceive('getCommonQueryFlags')
            ->with($application, $query)
            ->andReturn($flags);

        $result = $this->sut->handleQuery($query);

        $data = $result->serialize();

        $expected = [
            'foo' => 'bar',
            'showSmallTable' => true,
            'showMediumTable' => true,
            'showLargeTable' => true,
            'smallAuthExceeded' => true,
            'mediumAuthExceeded' => true,
            'largeAuthExceeded' => true,
            'availableSmallSpaces' => 9,
            'availableMediumSpaces' => 8,
            'availableLargeSpaces' => 7,
            'small' => [
                ['type' => 'small']
            ],
            'medium' => [
                ['type' => 'medium']
            ],
            'large' => [
                ['type' => 'large']
            ],
            'canTransfer' => false,
            'hasBreakdown' => false,
            'licenceVehicles' => ['results' => [['foo' => 'bar']], 'count' => 1],
            'activeVehicleCount' => 1,
            'allVehicleCount' => 3
        ];

        $this->assertEquals($expected, $data);
    }
}
