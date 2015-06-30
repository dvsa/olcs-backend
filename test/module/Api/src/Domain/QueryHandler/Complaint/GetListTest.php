<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Complaint;

use Dvsa\Olcs\Api\Domain\QueryHandler\Complaint\GetList as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\Complaint as Repo;
use Dvsa\Olcs\Transfer\Query\Complaint\GetList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;

/**
 * GetListTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Complaint', Repo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['QUERY']);

        $mock = \Mockery::mock();
        $mock->shouldReceive('serialize')->with(
            [
                'case',
                'complainantContactDetails' => [
                    'person'
                ],
                'ocComplaints' => [
                    'operatingCentre' => [
                        'address'
                    ]
                ]
            ]
        )->once()->andReturn('RESULT');

        $this->repoMap['Complaint']->shouldReceive('fetchList')->with($query, \Doctrine\ORM\Query::HYDRATE_OBJECT)
            ->andReturn([$mock]);
        $this->repoMap['Complaint']->shouldReceive('fetchCount')->with($query)->andReturn('COUNT');

        $result = $this->sut->handleQuery($query);

        $this->assertSame(['RESULT'], $result['result']);
        $this->assertSame('COUNT', $result['count']);
    }
}
