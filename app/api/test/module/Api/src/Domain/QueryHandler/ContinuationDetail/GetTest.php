<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\ContinuationDetail;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Dvsa\Olcs\Api\Domain\QueryHandler\ContinuationDetail\Get as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query\ContinuationDetail\Get as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * Continuation Get test
 */
class GetTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('ContinuationDetail', Repository\ContinuationDetail::class);
        $this->mockRepo('Document', Repository\Document::class);
        $this->mockedSmServices['FinancialStandingHelperService'] = m::mock();

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id'=> 123]);

        $continuationDetail = m::mock(BundleSerializableInterface::class);
        $continuationDetail->shouldReceive('getLicence->getOrganisation->getId')->with()->once()->andReturn(99);
        $continuationDetail->shouldReceive('getId')->with()->once()->andReturn(123);

        $this->repoMap['ContinuationDetail']
            ->shouldReceive('fetchUsingId')->with($query)->once()->andReturn($continuationDetail);
        $this->repoMap['Document']
            ->shouldReceive('fetchListForContinuationDetail')->with(123, Query::HYDRATE_ARRAY)->once()
            ->andReturn(['document1', 'document2']);

        $this->mockedSmServices['FinancialStandingHelperService']
            ->shouldReceive('getFinanceCalculationForOrganisation')->with(99)->once()->andReturn('123.99');

        $continuationDetail->shouldReceive('serialize')
            ->once()
            ->with(['licence' => ['trafficArea']])
            ->andReturn(['ENTITY']);

        $this->assertEquals(
            [
                'ENTITY',
                'financeRequired' => '123.99',
                'documents' => ['document1', 'document2'],
            ],
            $this->sut->handleQuery($query)->serialize()
        );
    }
}
