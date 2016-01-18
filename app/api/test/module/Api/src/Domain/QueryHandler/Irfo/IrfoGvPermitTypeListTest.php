<?php

/**
 * IrfoGvPermitType list test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Irfo;

use Dvsa\Olcs\Api\Domain\QueryHandler\Irfo\IrfoGvPermitTypeList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\IrfoGvPermitType as IrfoGvPermitTypeRepo;
use Dvsa\Olcs\Transfer\Query\Irfo\IrfoGvPermitTypeList as Qry;

/**
 * IrfoGvPermitType list test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class IrfoGvPermitTypeListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new IrfoGvPermitTypeList();
        $this->mockRepo('IrfoGvPermitType', IrfoGvPermitTypeRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $this->repoMap['IrfoGvPermitType']->shouldReceive('fetchList')
            ->with($query)
            ->andReturn(['foo']);

        $this->repoMap['IrfoGvPermitType']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['result'], ['foo']);
    }
}
