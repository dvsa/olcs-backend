<?php

/**
 * Complaint Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Complaint;

use Dvsa\Olcs\Api\Domain\QueryHandler\Complaint\Complaint;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Complaint as ComplaintRepo;
use Dvsa\Olcs\Transfer\Query\Complaint\Complaint as Qry;
use Mockery as m;

/**
 * Complaint Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class ComplaintTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new Complaint();
        $this->mockRepo('Complaint', ComplaintRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);

        $mockResult = m::mock('Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface');

        $this->repoMap['Complaint']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($mockResult);

        $result = $this->sut->handleQuery($query);
        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\QueryHandler\Result', $result);
    }
}
