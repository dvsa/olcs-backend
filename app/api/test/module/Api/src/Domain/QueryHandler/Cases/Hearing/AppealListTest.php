<?php

/**
 * AppealList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\Hearing;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Hearing\AppealList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Hearing as AppealRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Hearing\AppealList as Qry;

/**
 * AppealList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class AppealListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new AppealList();
        $this->mockRepo('Appeal', AppealRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $this->repoMap['Appeal']->shouldReceive('fetchList')
            ->with($query)
            ->andReturn(['foo']);

        $this->repoMap['Appeal']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['result'], ['foo']);
    }
}
