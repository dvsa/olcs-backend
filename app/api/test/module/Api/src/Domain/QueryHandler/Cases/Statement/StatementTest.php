<?php

/**
 * Statement Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\Statement;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Statement\Statement;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Statement as StatementRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Statement\Statement as Qry;
use Mockery as m;

/**
 * Statement Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class StatementTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Statement();
        $this->mockRepo('Statement', StatementRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);
        $mockResult = m::mock('Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface');

        $this->repoMap['Statement']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($mockResult);

        $result = $this->sut->handleQuery($query);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\QueryHandler\Result', $result);
    }
}
