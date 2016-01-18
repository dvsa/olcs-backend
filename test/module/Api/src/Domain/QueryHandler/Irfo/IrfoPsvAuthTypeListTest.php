<?php

/**
 * IrfoPsvAuthType list test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Irfo;

use Dvsa\Olcs\Api\Domain\QueryHandler\Irfo\IrfoPsvAuthTypeList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\IrfoPsvAuthType as IrfoPsvAuthTypeRepo;
use Dvsa\Olcs\Transfer\Query\Irfo\IrfoPsvAuthTypeList as Qry;

/**
 * IrfoPsvAuthType list test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class IrfoPsvAuthTypeListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new IrfoPsvAuthTypeList();
        $this->mockRepo('IrfoPsvAuthType', IrfoPsvAuthTypeRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $this->repoMap['IrfoPsvAuthType']->shouldReceive('fetchList')
            ->with($query)
            ->andReturn(['foo']);

        $this->repoMap['IrfoPsvAuthType']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['result'], ['foo']);
    }
}
