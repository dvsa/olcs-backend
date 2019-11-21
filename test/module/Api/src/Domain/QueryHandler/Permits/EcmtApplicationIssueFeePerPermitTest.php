<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\EcmtApplicationIssueFeePerPermit;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepo;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Query\Permits\EcmtApplicationIssueFeePerPermit as EcmtApplicationIssueFeePerPermitQuery;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class EcmtApplicationIssueFeePerPermitTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new EcmtApplicationIssueFeePerPermit();
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplicationRepo::class);
        $this->mockRepo('FeeType', FeeTypeRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $ecmtPermitApplicationId = 37;
        $feePerPermit = 123.00;
        $productReference = 'ISSUE_FEE_PRODUCT_REFERENCE';

        $query = EcmtApplicationIssueFeePerPermitQuery::create(['id' => $ecmtPermitApplicationId]);

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('getIssueFeeProductReference')
            ->withNoArgs()
            ->andReturn($productReference);

        $feeType = m::mock(FeeType::class);
        $feeType->shouldReceive('getFixedValue')
            ->withNoArgs()
            ->andReturn($feePerPermit);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($ecmtPermitApplication);

        $this->repoMap['FeeType']->shouldReceive('getLatestByProductReference')
            ->with($productReference)
            ->andReturn($feeType);

        $expectedResponse = [
            'feePerPermit' => $feePerPermit
        ];

        $this->assertEquals(
            $expectedResponse,
            $this->sut->handleQuery($query)
        );
    }
}
