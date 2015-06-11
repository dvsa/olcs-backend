<?php

/**
 * IrfoPsvAuth Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\Irfo\IrfoPsvAuth;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\IrfoPsvAuth as IrfoPsvAuthRepo;
use Dvsa\Olcs\Transfer\Query\Irfo\IrfoPsvAuth as Qry;

/**
 * IrfoPsvAuth Test
 */
class IrfoPsvAuthTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new IrfoPsvAuth();
        $this->mockRepo('IrfoPsvAuth', IrfoPsvAuthRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);

        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn(['foo']);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result, ['foo']);
    }
}
