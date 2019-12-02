<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\FeeType;

use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\QueryHandler\FeeType\GetDistinctList as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepo;
use Dvsa\Olcs\Transfer\Query\FeeType\GetDistinctList as ListQuery;

/**
 * GetDistinctList Test
 */
class GetDistinctListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('FeeType', FeeTypeRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $query = ListQuery::create([]);

        $item1 = 'ANN';
        $item2 = 'VAR';
        $feeTypes = [$item1, $item2];

        $this->repoMap['FeeType']
            ->shouldReceive('fetchDistinctFeeTypes')
            ->withNoArgs()
            ->once()
            ->andReturn($feeTypes);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'results' => [$item1, $item2]
        ];

        $this->assertEquals($expected, $result);
    }
}
