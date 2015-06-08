<?php

/**
 * Complaint Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\Complaint;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Complaint\Complaint;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Complaint as ComplaintRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Complaint\Complaint as Qry;

/**
 * Complaint Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class ComplaintTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Complaint();
        $this->mockRepo('Complaint', ComplaintRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);

        $this->repoMap['Complaint']->shouldReceive('fetchUsingCaseId')
            ->with($query)
            ->andReturn(['foo']);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result, ['foo']);
    }
}
