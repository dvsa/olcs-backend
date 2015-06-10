<?php

/**
 * ComplaintList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Complaint\ComplaintList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Complaint as ComplaintRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Complaint\ComplaintList as Qry;

/**
 * ComplaintList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class ComplaintListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ComplaintList();
        $this->mockRepo('Complaint', ComplaintRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $this->repoMap['Complaint']->shouldReceive('fetchList')
            ->with($query)
            ->andReturn(['foo']);

        $this->repoMap['Complaint']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['result'], ['foo']);
    }
}
