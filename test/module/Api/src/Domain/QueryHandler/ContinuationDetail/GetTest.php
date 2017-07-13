<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\ContinuationDetail;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Dvsa\Olcs\Api\Domain\QueryHandler\ContinuationDetail\Get as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\ContinuationDetail as ContinuationDetailRepo;
use Dvsa\Olcs\Api\Domain\Repository\SystemParameter as SystemParameterRepo;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
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
        $this->mockRepo('SystemParameter', SystemParameterRepo::class);
        $this->mockRepo('Fee', FeeRepo::class);
        $this->mockedSmServices['FinancialStandingHelperService'] = m::mock();

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id'=> 123]);

        $continuationDetail = m::mock(BundleSerializableInterface::class);
        $continuationDetail->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getOrganisation')
                    ->andReturn(
                        m::mock()
                        ->shouldReceive('getId')
                        ->andReturn(99)
                        ->once()
                        ->getMock()
                    )
                    ->once()
                    ->shouldReceive('getId')
                    ->andReturn(1)
                    ->once()
                    ->getMock()
            )
            ->once()
            ->shouldReceive('serialize')
            ->with(['licence' => ['organisation']])
            ->andReturn(['licence_entity'])
            ->once()
            ->getMock();

        $this->repoMap['ContinuationDetail']
            ->shouldReceive('fetchUsingId')->with($query)->once()->andReturn($continuationDetail);

        $this->repoMap['SystemParameter']
            ->shouldReceive('getDisableSelfServeCardPayments')
            ->andReturn(false)
            ->once();

        $mockFee = m::mock(BundleSerializableInterface::class)
            ->shouldReceive('serialize')
            ->with(['feeType' => ['feeType'], 'licence'])
            ->andReturn(['fee_entity'])
            ->once()
            ->getMock();

        $this->repoMap['Fee']
            ->shouldReceive('fetchOutstandingContinuationFeesByLicenceId')
            ->with(1)
            ->andReturn([$mockFee])
            ->once();

        $this->mockedSmServices['FinancialStandingHelperService']
            ->shouldReceive('getFinanceCalculationForOrganisation')->with(99)->once()->andReturn('123.99');

        $continuationDetail;

        $this->assertEquals(
            [
                'licence_entity',
                'financeRequired' => '123.99',
                'disableCardPayments' => false,
                'fees' => [
                    ['fee_entity']
                ]
            ],
            $this->sut->handleQuery($query)->serialize()
        );
    }
}
