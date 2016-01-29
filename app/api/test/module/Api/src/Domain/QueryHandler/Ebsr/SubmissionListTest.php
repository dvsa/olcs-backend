<?php

/**
 * SubmissionList Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Ebsr;

use Dvsa\Olcs\Api\Domain\QueryHandler\Ebsr\SubmissionList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\EbsrSubmission as EbsrSubmissionRepo;
use Dvsa\Olcs\Transfer\Query\Ebsr\SubmissionList as Qry;
use Mockery as m;

/**
 * SubmissionList Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class SubmissionListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new SubmissionList();
        $this->mockRepo('EbsrSubmission', EbsrSubmissionRepo::class);

        parent::setUp();
    }

    /**
     * Tests handleQuery
     */
    public function testHandleQuery()
    {
        $count = 25;
        $query = Qry::create([]);

        $mockResult = m::mock();
        $mockResult->shouldReceive('serialize')->once()->andReturn('foo');

        $this->repoMap['EbsrSubmission']->shouldReceive('fetchList')
            ->with($query, m::type('integer'))
            ->andReturn([$mockResult]);

        $this->repoMap['EbsrSubmission']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn($count);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], $count);
        $this->assertEquals($result['result'], ['foo']);
    }
}
