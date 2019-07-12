<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\AvailableYears;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Transfer\Query\Permits\AvailableYears as AvailableYearsQuery;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use DateTime;

class AvailableYearsTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new AvailableYears();
        $this->mockRepo('IrhpPermitWindow', IrhpPermitWindowRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $ipw1 = m::mock(IrhpPermitWindow::class);
        $ipw2 = m::mock(IrhpPermitWindow::class);

        $irhpPermitWindows =
            [
                $ipw1,
                $ipw2
            ];

        $query = AvailableYearsQuery::create(
            [
                'type' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
            ]
        );

        $this->repoMap['IrhpPermitWindow']->shouldReceive('fetchOpenWindowsByType')
            ->with(
                $query->getType(),
                m::type(DateTime::class)
            )
            ->andReturn($irhpPermitWindows);

        $ipw1->shouldReceive('getIrhpPermitStock->getValidTo->format')
            ->with('Y')
            ->once()
            ->andReturn(3030);
        $ipw2->shouldReceive('getIrhpPermitStock->getValidTo->format')
            ->once()
            ->with('Y')
            ->andReturn(3031);

        $this->assertEquals(
            [
                'years' => [3030, 3031]
            ],
            $this->sut->handleQuery($query)
        );
    }

    /**
     * @dataProvider dpTestHandleQueryUnsupportedType
     */
    public function testHandleQueryUnsupportedType($unsupportedTypeId)
    {
        $query = AvailableYearsQuery::create(
            [
                'type' => $unsupportedTypeId
            ]
        );

        $this->assertEquals(
            [
                'years' => []
            ],
            $this->sut->handleQuery($query)
        );
    }

    public function dpTestHandleQueryUnsupportedType()
    {
        return [
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL],
        ];
    }

    public function testHandleQueryNoYears()
    {
        $query = AvailableYearsQuery::create(
            [
                'type' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
            ]
        );

        $this->repoMap['IrhpPermitWindow']->shouldReceive('fetchOpenWindowsByType')
            ->with(
                $query->getType(),
                m::type(DateTime::class)
            )
            ->andReturn([]);


        $this->assertEquals(
            [
                'years' => []
            ],
            $this->sut->handleQuery($query)
        );
    }
}
