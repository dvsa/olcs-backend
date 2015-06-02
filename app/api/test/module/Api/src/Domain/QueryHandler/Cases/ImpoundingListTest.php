<?php

/**
 * ImpoundingList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\ImpoundingList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Impounding as ImpoundingRepo;
use Dvsa\Olcs\Transfer\Query\Cases\ImpoundingList as Qry;

/**
 * ImpoundingList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class ImpoundingListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ImpoundingList();
        $this->mockRepo('Impounding', ImpoundingRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $this->repoMap['Impounding']->shouldReceive('fetchList')
            ->with($query)
            ->andReturn(['foo']);

        $this->repoMap['Impounding']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['result'], ['foo']);
    }
}
