<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\GracePeriod;

use Doctrine\ORM\Internal\Hydration\IterableResult;
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
    public function setUp()
    {
        $this->sut = new Correspondences();

        $this->mockRepo('Correspondence', Repository\Correspondence::class);
        $this->mockRepo('Fee', Repository\Fee::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['organisation' => 1]);

        $row = [
            'id' => 9999,
            'accessed' => 'unit_Accessed',
            'createdOn' => 'unit_CreatedOn',
            'licId' => 'unit_licId',
            'licNo' => 'unit_licNo',
            'licStatus' => 'unit_licStatus',
            'docDesc' => 'unit_docDesc',
        ];

        $mockIterResult = m::mock(IterableResult::class)
            ->shouldReceive('next')->once()->andReturn([$row])
            ->shouldReceive('next')->once()->andReturn(false)
            ->getMock();

        $this->repoMap['Correspondence']
            ->shouldReceive('fetchDocumentsList')
            ->with($query)
            ->andReturn($mockIterResult);

        $this->repoMap['Fee']
            ->shouldReceive('getOutstandingFeeCountByOrganisationId')
            ->with(1, true)
            ->andReturn(66);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'result' => [
                    [
                        'id' => 9999,
                        'accessed' => 'unit_Accessed',
                        'createdOn' => 'unit_CreatedOn',

                        'licence' => [
                            'id' => 'unit_licId',
                            'licNo' => 'unit_licNo',
                            'status' => 'unit_licStatus',
                        ],
                        'document' => [
                            'description' => 'unit_docDesc',
                        ],
                    ],
                ],
                'count' => 1,
                'feeCount' => 66,
            ],
            $result
        );
    }
}
