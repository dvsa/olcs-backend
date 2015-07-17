<?php

/**
 * SubmissionAction Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Submission;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Submission\SubmissionAction;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Dvsa\Olcs\Api\Domain\Repository\SubmissionAction as SubmissionActionRepo;
use Dvsa\Olcs\Transfer\Query\Submission\SubmissionAction as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;

/**
 * SubmissionAction Test
 */
class SubmissionActionTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new SubmissionAction();
        $this->mockRepo('SubmissionAction', SubmissionActionRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);

        $this->repoMap['SubmissionAction']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn(
                m::mock(BundleSerializableInterface::class)
                    ->shouldReceive('serialize')
                    ->andReturn(['foo'])
                    ->getMock()
            );

        $result = $this->sut->handleQuery($query);
        $this->assertEquals(['foo'], $result->serialize());
    }
}
