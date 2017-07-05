<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\ContinuationDetail;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Dvsa\Olcs\Api\Domain\QueryHandler\ContinuationDetail\Get as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\ContinuationDetail as ContinuationDetailRepo;
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
        $this->mockRepo('ContinuationDetail', ContinuationDetailRepo::class);
        $this->mockedSmServices['FinancialStandingHelperService'] = m::mock();

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id'=> 123]);

        $continuationDetail = m::mock(BundleSerializableInterface::class);
        $continuationDetail->shouldReceive('getLicence->getOrganisation->getId')->with()->once()->andReturn(99);

        $this->repoMap['ContinuationDetail']
            ->shouldReceive('fetchUsingId')->with($query)->once()->andReturn($continuationDetail);

        $this->mockedSmServices['FinancialStandingHelperService']
            ->shouldReceive('getFinanceCalculationForOrganisation')->with(99)->once()->andReturn('123.99');

        $continuationDetail->shouldReceive('serialize')
            ->once()
            ->with(['licence'])
            ->andReturn(['ENTITY']);

        $this->assertEquals(
            [
                'ENTITY',
                'financeRequired' => 123.99
            ],
            $this->sut->handleQuery($query)->serialize()
        );
    }
}
