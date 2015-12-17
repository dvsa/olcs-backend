<?php

/**
 * Get Presiding TC list test
 *
 * @author Alex Peshkov <alex.pehkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\PresidingTc;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\PresidingTc\GetList as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\PresidingTc as Repo;
use Dvsa\Olcs\Transfer\Query\Cases\PresidingTc\GetList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Pi\PresidingTc as PresidingTcEntity;

/**
 * Get Presiding TC list test
 *
 * @author Alex Peshkov <alex.pehkov@valtech.co.uk>
 */
class GetListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('PresidingTc', Repo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['QUERY']);

        $presidingTcEntity = m::mock(PresidingTcEntity::class);
        $presidingTcEntity->shouldReceive('serialize')->once()->andReturn('SERIALIZED');

        $this->repoMap['PresidingTc']->shouldReceive('fetchList')
            ->with($query, \Doctrine\ORM\Query::HYDRATE_OBJECT)->andReturn([$presidingTcEntity]);
        $this->repoMap['PresidingTc']->shouldReceive('fetchCount')->with($query)->andReturn('COUNT');

        $result = $this->sut->handleQuery($query);

        $this->assertSame(['SERIALIZED'], $result['result']);
        $this->assertSame('COUNT', $result['count']);
    }
}
