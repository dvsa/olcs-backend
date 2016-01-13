<?php

/**
 * Continuation details - get list test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\ContinuationDetail;

use Dvsa\Olcs\Api\Domain\QueryHandler\ContinuationDetail\GetList as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\ContinuationDetail as ContinuationDetailRepo;
use Dvsa\Olcs\Api\Domain\Repository\Continuation as ContinuationRepo;
use Dvsa\Olcs\Transfer\Query\ContinuationDetail\GetList as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * Continuation details - get list test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DiscPrefixesTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Continuation', ContinuationRepo::class);
        $this->mockRepo('ContinuationDetail', ContinuationDetailRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $continuationId = 1;
        $licenceStatus = 'lsts_valid';
        $licenceNo = 'OB1';
        $method = 'email';
        $status = 'con_det_sts_printed';

        $query = Qry::create(
            [
                'continuationId' => $continuationId,
                'licenceStatus'  => $licenceStatus,
                'licenceNo'      => $licenceNo,
                'method'         => $method,
                'status'         => $status
            ]
        );

        $this->repoMap['ContinuationDetail']
            ->shouldReceive('fetchDetails')
            ->with($continuationId, $licenceStatus, $licenceNo, $method, $status)
            ->once()
            ->andReturn(['details']);

        $this->repoMap['Continuation']
            ->shouldReceive('fetchWithTa')
            ->with($continuationId)
            ->once()
            ->andReturn(['header']);

        $this->assertEquals(
            ['result' => ['details'], 'count' => 1, 'header' => ['header']],
            $this->sut->handleQuery($query)
        );
    }
}
