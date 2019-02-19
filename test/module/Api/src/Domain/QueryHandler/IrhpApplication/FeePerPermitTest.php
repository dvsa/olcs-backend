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

    public function testHandleQuery()
    {
        $irhpApplicationId = 52;
        $applicationFeeTypeProductRef = 'applicationFeeTypeProductRef';
        $issueFeeTypeProductRef = 'issueFeeTypeProductRef';
        $feePerPermit = 50;

        $applicationFeeType = m::mock(FeeType::class);
        $issueFeeType = m::mock(FeeType::class);

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getApplicationFeeTypeProductReference')
            ->andReturn($applicationFeeTypeProductRef);
        $irhpApplication->shouldReceive('getIssueFeeTypeProductReference')
            ->andReturn($issueFeeTypeProductRef);
        $irhpApplication->shouldReceive('getFeePerPermit')
            ->with($applicationFeeType, $issueFeeType)
            ->andReturn($feePerPermit);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $this->repoMap['FeeType']->shouldReceive('getLatestByProductReference')
            ->with($applicationFeeTypeProductRef)
            ->andReturn($applicationFeeType);

        $this->repoMap['FeeType']->shouldReceive('getLatestByProductReference')
            ->with($issueFeeTypeProductRef)
            ->andReturn($issueFeeType);

        $expectedResult = ['feePerPermit' => $feePerPermit];
        $result = $this->sut->handleQuery(FeePerPermitQry::create(['id' => $irhpApplicationId]));

        $this->assertEquals($expectedResult, $result);
    }
}
