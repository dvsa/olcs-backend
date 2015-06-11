<?php

/**
 * EnvironmentalComplaintList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\EnvironmentalComplaint\EnvironmentalComplaintList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Complaint as ComplaintRepo;
use Dvsa\Olcs\Transfer\Query\Cases\EnvironmentalComplaint\EnvironmentalComplaintList as Qry;

/**
 * EnvironmentalComplaintList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class EnvironmentalComplaintListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new EnvironmentalComplaintList();
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
