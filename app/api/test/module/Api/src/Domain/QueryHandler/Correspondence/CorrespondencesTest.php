<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Correspondence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Dvsa\Olcs\Api\Domain\QueryHandler\Correspondence\Correspondences;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query\Correspondence\Correspondences as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Api\Domain\QueryHandler\Correspondence\Correspondences
 */
class CorrespondencesTest extends QueryHandlerTestCase
{
    const ORG_ID = 9999;

    public function setUp()
    {
        $this->sut = new Correspondences();

        $this->mockRepo('Correspondence', Repository\Correspondence::class);
        $this->mockRepo('Fee', Repository\Fee::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['organisation' => self::ORG_ID]);

        $mockEntity = m::mock(BundleSerializableInterface::class)
            ->shouldReceive('serialize')
            ->times(2)
            ->with(['licence', 'document'])
            ->andReturn('EXPECT_ENTITY')
            ->getMock();

        $this->repoMap['Correspondence']
            ->shouldReceive('fetchList')
            ->once()
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn([$mockEntity, clone $mockEntity])
            ->shouldReceive('fetchCount')->once()->andReturn(2);

        $feeCnt = 123;
        $this->repoMap['Fee']
            ->shouldReceive('getOutstandingFeeCountByOrganisationId')
            ->with(self::ORG_ID, true, true)
            ->andReturn($feeCnt);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'result' => [
                    'EXPECT_ENTITY',
                    'EXPECT_ENTITY',
                ],
                'count' => 2,
                'feeCount' => $feeCnt,
            ],
            $result
        );
    }
}
