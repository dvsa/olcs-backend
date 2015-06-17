<?php

/**
 * OppositionList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Opposition\OppositionList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Opposition as OppositionRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Opposition\OppositionList as Qry;

/**
 * OppositionList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class OppositionListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new OppositionList();
        $this->mockRepo('Opposition', OppositionRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $this->repoMap['Opposition']->shouldReceive('fetchList')
            ->with($query)
            ->andReturn(['foo']);

        $this->repoMap['Opposition']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['result'], ['foo']);
    }
}
