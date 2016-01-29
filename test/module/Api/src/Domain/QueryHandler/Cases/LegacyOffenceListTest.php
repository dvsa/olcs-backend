<?php

/**
 * LegacyOffenceList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\LegacyOffenceList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\LegacyOffence as LegacyOffenceRepo;
use Dvsa\Olcs\Transfer\Query\Cases\LegacyOffenceList as Qry;

/**
 * LegacyOffenceList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class LegacyOffenceListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new LegacyOffenceList();
        $this->mockRepo('LegacyOffence', LegacyOffenceRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $this->repoMap['LegacyOffence']->shouldReceive('fetchList')
            ->with($query)
            ->andReturn(['foo']);

        $this->repoMap['LegacyOffence']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['result'], ['foo']);
    }
}
