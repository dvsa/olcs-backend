<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Irfo;

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * IrfoGvPermitType list test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class IrfoGvPermitTypeListTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new QueryHandler\Irfo\IrfoGvPermitTypeList();
        $this->mockRepo('IrfoGvPermitType', Repository\IrfoGvPermitType::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query\Irfo\IrfoGvPermitTypeList::create([]);

        $entity = m::mock(\Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermitType::class)
            ->shouldReceive('serialize')
            ->with([])
            ->once()
            ->andReturn('SERIALIZED')
            ->getMock();

        $this->repoMap['IrfoGvPermitType']
            ->shouldReceive('fetchList')
            ->with($query, \Doctrine\ORM\Query::HYDRATE_OBJECT)
            ->andReturn([$entity])
            //
            ->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $actual = $this->sut->handleQuery($query);

        static::assertEquals(2, $actual['count']);
        static::assertEquals(['SERIALIZED'], $actual['result']);
    }
}
