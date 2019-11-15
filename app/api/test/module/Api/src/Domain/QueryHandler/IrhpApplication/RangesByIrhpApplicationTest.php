<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpApplication;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication\RangesByIrhpApplication as RangesByIrhpApplicationHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\RangesByIrhpApplication as QryClass;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * RangesByIrhpApplication Test
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class RangesByIrhpApplicationTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new RangesByIrhpApplicationHandler();
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);

        parent::setUp();
    }

    /**
     * @dataProvider dpTestHandleQuery
     */
    public function testHandleQuery($ranges, $expected)
    {
        $query = QryClass::create(['irhpApplication' => 100011]);

        $irhpApplication = m::mock(IrhpApplicationEntity::class);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($query->getIrhpApplication())
            ->andReturn($irhpApplication);

        $irhpApplication
            ->shouldReceive('getAssociatedStock->getNonReservedNonReplacementRangesOrderedByFromNo')
            ->once()
            ->withNoArgs()
            ->andReturn($ranges);

        $this->assertEquals(
            $expected,
            $this->sut->handleQuery($query)
        );
    }

    public function dpTestHandleQuery()
    {
        $range1 = m::mock(IrhpPermitRange::class);
        $range1->shouldReceive('serialize')
        ->andReturn($range1);

        $range2 = m::mock(IrhpPermitRange::class);
        $range2->shouldReceive('serialize')
        ->andReturn($range2);

        $ranges = new ArrayCollection([$range1, $range2]);
        return [
            [
                $ranges,
                [
                    'count' => 2,
                    'ranges' => $ranges->toArray()
                ]
            ],
            [
                new ArrayCollection(),
                [
                    'count' => 0,
                    'ranges' => []
                ]
            ]
        ];
    }
}
