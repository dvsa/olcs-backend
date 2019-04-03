<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication\FeePerPermit;
use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\FeePerPermit as FeePerPermitQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class FeePerPermitTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new FeePerPermit();

        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);
        $this->mockRepo('FeeType', FeeTypeRepo::class);

        parent::setUp();
    }

    public function testHandleQueryBilateral()
    {
        $irhpApplicationId = 52;

        $applicationFeeType = m::mock(FeeType::class);
        $applicationFeeType->shouldReceive('getFixedValue')
            ->andReturn(45);

        $issueFeeType = m::mock(FeeType::class);
        $issueFeeType->shouldReceive('getFixedValue')
            ->andReturn(27);

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->andReturn(4);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $this->repoMap['FeeType']->shouldReceive('getLatestByProductReference')
            ->with(FeeType::FEE_TYPE_IRHP_APP_BILATERAL_PRODUCT_REF)
            ->andReturn($applicationFeeType);

        $this->repoMap['FeeType']->shouldReceive('getLatestByProductReference')
            ->with(FeeType::FEE_TYPE_IRHP_ISSUE_BILATERAL_PRODUCT_REF)
            ->andReturn($issueFeeType);

        $expectedResult = ['feePerPermit' => 72];
        $result = $this->sut->handleQuery(FeePerPermitQry::create(['id' => $irhpApplicationId]));

        $this->assertEquals($expectedResult, $result);
    }

    public function testHandleQueryNotBilateral()
    {
        $irhpApplicationId = 52;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->andReturn(5);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $expectedResult = ['feePerPermit' => 'Not applicable'];
        $result = $this->sut->handleQuery(FeePerPermitQry::create(['id' => $irhpApplicationId]));

        $this->assertEquals($expectedResult, $result);
    }
}
