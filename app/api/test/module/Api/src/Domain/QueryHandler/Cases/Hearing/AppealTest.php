<?php

/**
 * Appeal Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\Hearing;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Hearing\Appeal;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Appeal as AppealRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Hearing\Appeal as Qry;

/**
 * Appeal Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class AppealTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Appeal();
        $this->mockRepo('Appeal', AppealRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);

        $this->repoMap['Appeal']->shouldReceive('fetchUsingCaseId')
            ->with($query)
            ->andReturn(['foo']);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result, ['foo']);
    }
}
