<?php

/**
 * Opposition Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\Opposition;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Opposition\Opposition;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Opposition as OppositionRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Opposition\Opposition as Qry;

/**
 * Opposition Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class OppositionTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Opposition();
        $this->mockRepo('Opposition', OppositionRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);

        $this->repoMap['Opposition']->shouldReceive('fetchUsingCaseId')
            ->with($query)
            ->andReturn(['foo']);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result, ['foo']);
    }
}
