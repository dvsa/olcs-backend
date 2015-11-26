<?php

/**
 * CorrespondencesTest.php
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\GracePeriod;

use Doctrine\ORM\Query;

use Dvsa\Olcs\Api\Domain\QueryHandler\Correspondence\Correspondences;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Correspondence as CorrespondenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Dvsa\Olcs\Transfer\Query\Correspondence\Correspondences as Qry;
use Mockery as m;

/**
 * Correspondences Test
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class CorrespondencesTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Correspondences();
        $this->mockRepo('Correspondence', CorrespondenceRepo::class);
        $this->mockRepo('Fee', FeeRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['organisation' => 1]);

        $this->repoMap['Correspondence']
            ->shouldReceive('fetchList')
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn(
                [
                    m::mock()
                        ->shouldReceive('serialize')
                        ->andReturn(
                            [
                                'id' => 1
                            ]
                        )
                        ->getMock(),
                    m::mock()
                        ->shouldReceive('serialize')
                        ->andReturn(
                            [
                                'id' => 2
                            ]
                        )
                        ->getMock()
                ]
            )
            ->shouldReceive('fetchCount')
            ->andReturn(2);

        $this->repoMap['Fee']
            ->shouldReceive('getOutstandingFeeCountByOrganisationId')
            ->with(1, true)
            ->andReturn(66);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'result' => [
                    [
                        'id' => 1
                    ],
                    [
                        'id' => 2
                    ]
                ],
                'count' => 2,
                'feeCount' => 66,
            ],
            $result
        );
    }
}
