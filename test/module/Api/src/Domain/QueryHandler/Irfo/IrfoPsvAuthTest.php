<?php

/**
 * IrfoPsvAuth Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Irfo;

use Dvsa\Olcs\Api\Domain\QueryHandler\Irfo\IrfoPsvAuth;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\IrfoPsvAuth as IrfoPsvAuthRepo;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Dvsa\Olcs\Transfer\Query\Irfo\IrfoPsvAuth as Qry;
use Mockery as m;

/**
 * IrfoPsvAuth Test
 */
class IrfoPsvAuthTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new IrfoPsvAuth();
        $this->mockRepo('IrfoPsvAuth', IrfoPsvAuthRepo::class);
        $this->mockRepo('Fee', FeeRepo::class);

        parent::setUp();
    }

    public function testHandleQueryGrantable()
    {
        $id = 4;
        $query = Qry::create(['id' => $id]);

        $mockIrfoPsvAuth = m::mock('\Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth')->makePartial();
        $mockIrfoPsvAuth->setId($id);

        $status = new RefData('irfo_auth_s_pending');
        $mockIrfoPsvAuth->setStatus($status);

        $mockApplicationFee = m::mock('\Dvsa\Olcs\Api\Entity\Fee\Fee')->makePartial();

        $feeStatus = new RefData(Fee::STATUS_PAID);
        $mockApplicationFee->setFeeStatus($feeStatus);

        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($mockIrfoPsvAuth);

        $this->repoMap['Fee']->shouldReceive('fetchApplicationFeeByPsvAuthId')
            ->with($id)
            ->andReturn($mockApplicationFee);

        $result = $this->sut->handleQuery($query);

        $actions = $result->serialize()['actions'];

        $this->assertContains('grant', $actions);
    }

    public function testHandleQueryNotGrantable()
    {
        $id = 4;
        $query = Qry::create(['id' => $id]);

        $mockIrfoPsvAuth = m::mock('\Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth')->makePartial();
        $mockIrfoPsvAuth->setId($id);

        $status = new RefData('irfo_auth_s_pending');
        $mockIrfoPsvAuth->setStatus($status);

        $mockApplicationFee = m::mock('\Dvsa\Olcs\Api\Entity\Fee\Fee')->makePartial();

        $feeStatus = new RefData('feeStatus');
        $mockApplicationFee->setFeeStatus($feeStatus);

        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($mockIrfoPsvAuth);

        $this->repoMap['Fee']->shouldReceive('fetchApplicationFeeByPsvAuthId')
            ->with($id)
            ->andReturn($mockApplicationFee);

        $result = $this->sut->handleQuery($query);

        $actions = $result->serialize()['actions'];

        $this->assertNotContains('grant', $actions);

    }
}
