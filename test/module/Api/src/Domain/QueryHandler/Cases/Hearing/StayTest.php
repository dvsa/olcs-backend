<?php

/**
 * Stay Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\Hearing;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Hearing\Stay;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Stay as StayRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Hearing\Stay as Qry;

/**
 * Stay Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class StayTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Stay();
        $this->mockRepo('Stay', StayRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);

        $this->repoMap['Stay']->shouldReceive('fetchUsingCaseId')
            ->with($query)
            ->andReturn(['foo']);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result, ['foo']);
    }
}
