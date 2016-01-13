<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Cases;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CasesRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Cases as Qry;
use Mockery as m;

/**
 * Cases test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CasesTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Cases();
        $this->mockRepo('Cases', CasesRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 24]);

        $mockCase = m::mock(\Dvsa\Olcs\Api\Entity\Cases\Cases::class);
        $mockCase->shouldReceive('serialize')->andReturn(['SERIALIZED']);

        $this->repoMap['Cases']->shouldReceive('fetchUsingId')->with($query)->andReturn($mockCase);

        $result = $this->sut->handleQuery($query);

        $this->assertSame(['SERIALIZED'], $result->serialize());
        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\QueryHandler\Result', $result);
    }
}
