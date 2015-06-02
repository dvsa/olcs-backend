<?php

/**
 * IrfoGvPermit Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\Irfo\IrfoGvPermit;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\IrfoGvPermit as IrfoGvPermitRepo;
use Dvsa\Olcs\Transfer\Query\Irfo\IrfoGvPermit as Qry;

/**
 * IrfoGvPermit Test
 */
class IrfoGvPermitTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new IrfoGvPermit();
        $this->mockRepo('IrfoGvPermit', IrfoGvPermitRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);

        $this->repoMap['IrfoGvPermit']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn(['foo']);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result, ['foo']);
    }
}
