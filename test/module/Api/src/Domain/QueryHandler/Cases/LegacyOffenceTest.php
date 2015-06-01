<?php

/**
 * LegacyOffence Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\LegacyOffence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\LegacyOffence as LegacyOffenceRepo;
use Dvsa\Olcs\Transfer\Query\Cases\LegacyOffence as Qry;

/**
 * LegacyOffence Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class LegacyOffenceTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new LegacyOffence();
        $this->mockRepo('LegacyOffence', LegacyOffenceRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);

        $this->repoMap['LegacyOffence']->shouldReceive('fetchCaseLegacyOffenceUsingId')
            ->with($query)
            ->andReturn(['foo']);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result, ['foo']);
    }
}
