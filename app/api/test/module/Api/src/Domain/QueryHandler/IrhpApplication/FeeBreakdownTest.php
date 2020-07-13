<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication\FeeBreakdown;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Service\Permits\FeeBreakdown\BilateralFeeBreakdownGenerator;
use Dvsa\Olcs\Api\Service\Permits\FeeBreakdown\MultilateralFeeBreakdownGenerator;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class FeeBreakdownTest extends QueryHandlerTestCase
{
    private $irhpApplication;

    private $query;

    public function setUp(): void
    {
        $this->irhpApplication = m::mock(IrhpApplication::class);

        $this->query = m::mock(QueryInterface::class);

        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchUsingId')
            ->with($this->query)
            ->andReturn($this->irhpApplication);

        $this->sut = new FeeBreakdown();

        $this->mockedSmServices = [
            'PermitsBilateralFeeBreakdownGenerator' => m::mock(BilateralFeeBreakdownGenerator::class),
            'PermitsMultilateralFeeBreakdownGenerator' => m::mock(MultilateralFeeBreakdownGenerator::class),
        ];

        parent::setUp();
    }

    /**
     * @dataProvider dpGenerateFeeBreakdownAvailable
     */
    public function testGenerateFeeBreakdownAvailable($irhpPermitTypeId, $expectedServiceName)
    {
        $feeBreakdown = [
            'row1' => [
                'row1_column1' => 'row1_value1',
                'row1_column1' => 'row1_value1',
            ],
            'row1' => [
                'row1_column1' => 'row1_value1',
                'row1_column1' => 'row1_value1',
            ],
        ];

        $this->irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->withNoArgs()
            ->andReturn($irhpPermitTypeId);

        $this->mockedSmServices[$expectedServiceName]->shouldReceive('generate')
            ->with($this->irhpApplication)
            ->once()
            ->andReturn($feeBreakdown);

        $this->assertEquals(
            $feeBreakdown,
            $this->sut->handleQuery($this->query)
        );
    }

    public function dpGenerateFeeBreakdownAvailable()
    {
        return [
            [
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'PermitsBilateralFeeBreakdownGenerator',
            ],
            [
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                'PermitsMultilateralFeeBreakdownGenerator',
            ]
        ];
    }

    /**
     * @dataProvider dpGenerateFeeBreakdownNotAvailable
     */
    public function testGenerateFeeBreakdownNotAvailable($irhpPermitTypeId)
    {
        $this->irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->withNoArgs()
            ->andReturn($irhpPermitTypeId);

        $this->assertEquals(
            [],
            $this->sut->handleQuery($this->query)
        );
    }

    public function dpGenerateFeeBreakdownNotAvailable()
    {
        return [
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL],
        ];
    }
}
