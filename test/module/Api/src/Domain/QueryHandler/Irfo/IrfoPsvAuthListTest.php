<?php

/**
 * IrfoPsvAuthList Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Irfo;

use Dvsa\Olcs\Api\Domain\QueryHandler\Irfo\IrfoPsvAuthList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\IrfoPsvAuth as IrfoPsvAuthRepo;
use Dvsa\Olcs\Transfer\Query\Irfo\IrfoPsvAuthList as Qry;

/**
 * IrfoPsvAuthList Test
 */
class IrfoPsvAuthListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new IrfoPsvAuthList();
        $this->mockRepo('IrfoPsvAuth', IrfoPsvAuthRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchList')
            ->with($query)
            ->andReturn(['foo']);

        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['result'], ['foo']);
    }
}
