<?php

/**
 * IrfoGvPermitList Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Irfo;

use Dvsa\Olcs\Api\Domain\QueryHandler\Irfo\IrfoGvPermitList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\IrfoGvPermit as IrfoGvPermitRepo;
use Dvsa\Olcs\Transfer\Query\Irfo\IrfoGvPermitList as Qry;

/**
 * IrfoGvPermitList Test
 */
class IrfoGvPermitListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new IrfoGvPermitList();
        $this->mockRepo('IrfoGvPermit', IrfoGvPermitRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $this->repoMap['IrfoGvPermit']->shouldReceive('fetchList')
            ->with($query)
            ->andReturn(['foo']);

        $this->repoMap['IrfoGvPermit']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['result'], ['foo']);
    }
}
