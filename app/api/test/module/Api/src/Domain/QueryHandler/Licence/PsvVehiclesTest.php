<?php

/**
 * Psv Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\PsvVehicles;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\Service\PsvVehicles\PsvVehiclesQueryHelper;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query\Licence\PsvVehicles as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

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
        $this->mockRepo('Licence', Repository\Licence::class);
        $this->mockedSmServices['PsvVehiclesQueryHelper'] = m::mock(PsvVehiclesQueryHelper::class);

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

        /** @var Entity\Licence\Licence|m\MockInterface $licence */
        $licence = m::mock(Entity\Licence\Licence::class)->makePartial();
        $licence->shouldReceive('hasPsvBreakdown')->andReturn(false);
        $licence->shouldReceive('getOtherActiveLicences->isEmpty')->andReturn(true);
        $licence->shouldReceive('serialize')
            ->with(['organisation'])
            ->andReturn(['foo' => 'bar']);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($licence);

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
            ->with($licence, $query)
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
            'hasBreakdown' => false
        ];

        $this->assertEquals($expected, $data);
    }
}
