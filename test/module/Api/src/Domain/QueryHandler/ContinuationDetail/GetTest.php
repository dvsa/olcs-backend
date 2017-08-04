<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\ContinuationDetail;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Dvsa\Olcs\Api\Domain\QueryHandler\ContinuationDetail\Get as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\ContinuationDetail as ContinuationDetailRepo;
use Dvsa\Olcs\Api\Domain\Repository\SystemParameter as SystemParameterRepo;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Dvsa\Olcs\Api\Domain\Repository\Document as DocumentRepo;
use Dvsa\Olcs\Transfer\Query\ContinuationDetail\Get as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Continuation Get test
 */
class GetTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('ContinuationDetail', ContinuationDetailRepo::class);
        $this->mockRepo('Document', DocumentRepo::class);
        $this->mockRepo('SystemParameter', SystemParameterRepo::class);
        $this->mockRepo('Fee', FeeRepo::class);
        $this->mockedSmServices['FinancialStandingHelperService'] = m::mock();
        $this->mockedSmServices['Review\ApplicationUndertakings'] = m::mock();

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
                            ->shouldReceive('getType')->with()->andReturn(
                                m::mock()->shouldReceive('getId')->andReturn('ORG_TYPE_ID')->getMock()
                            )->once()
                        ->shouldReceive('getId')->with()->andReturn(99)->once()
                        ->getMock()
                    )
                    ->twice()
                    ->shouldReceive('getId')
                    ->andReturn(1)
                    ->twice()
                    ->getMock()
            )
            ->times(2)
            ->shouldReceive('serialize')
            ->with(['licence' => ['organisation', 'trafficArea']])
            ->andReturn(['licence_entity'])
            ->once()
            ->shouldReceive('getId')
            ->andReturn(123)
            ->once()
            ->shouldReceive('getAverageBalanceAmount')
            ->andReturn(0)
            ->once()
            ->shouldReceive('getFactoringAmount')
            ->andReturn(0)
            ->once()
            ->shouldReceive('getOverdraftAmount')
            ->andReturn(0)
            ->once()
            ->shouldReceive('getOtherFinancesAmount')
            ->andReturn(0)
            ->once()
            ->getMock();

        $continuationDetail->shouldReceive('getDigitalSignature')->with()->once()->andReturn(null);

        $this->repoMap['ContinuationDetail']
            ->shouldReceive('fetchUsingId')->with($query)->once()->andReturn($continuationDetail);

        $this->repoMap['Document']
            ->shouldReceive('fetchListForContinuationDetail')->with(123, Query::HYDRATE_ARRAY)->once()
            ->andReturn(['document1', 'document2']);

        $this->repoMap['SystemParameter']
            ->shouldReceive('getDisableSelfServeCardPayments')
            ->andReturn(false)
            ->once();
        $this->repoMap['SystemParameter']
            ->shouldReceive('getDisableGdsVerifySignatures')
            ->andReturn('DISABLE_SIGNATURES')
            ->once();

        $mockFee = m::mock(BundleSerializableInterface::class)
            ->shouldReceive('serialize')
            ->with(['feeType' => ['feeType'], 'licence'])
            ->andReturn(['fee_entity'])
            ->once()
            ->getMock();

        $mockContinuationFee = m::mock()
            ->shouldReceive('getLatestTransaction')
            ->andReturn(
                m::mock()
                ->shouldReceive('getCompletedDate')
                ->with(true)
                ->andReturn(new DateTime('now'))
                ->once()
                ->getMock()
            )
            ->once()
            ->shouldReceive('getLatestPaymentRef')
            ->andReturn('OLCS-12345')
            ->once()
            ->getMock();

        $this->repoMap['Fee']
            ->shouldReceive('fetchOutstandingContinuationFeesByLicenceId')
            ->with(1)
            ->andReturn([$mockFee])
            ->once()
            ->shouldReceive('fetchLatestPaidContinuationFee')
            ->with(1)
            ->andReturn($mockContinuationFee)
            ->once();

        $this->mockedSmServices['FinancialStandingHelperService']
            ->shouldReceive('getFinanceCalculationForOrganisation')->with(99)->once()->andReturn('123.99');

        $this->mockedSmServices['Review\ApplicationUndertakings']
            ->shouldReceive('getMarkupForLicence')->with($continuationDetail->getLicence())->once()
            ->andReturn('DECLARATIONS');

        $this->assertEquals(
            [
                'licence_entity',
                'financeRequired' => '123.99',
                'disableCardPayments' => false,
                'fees' => [
                    ['fee_entity']
                ],
                'documents' => ['document1', 'document2'],
                'organisationTypeId' => 'ORG_TYPE_ID',
                'declarations' => 'DECLARATIONS',
                'disableSignatures' => 'DISABLE_SIGNATURES',
                'hasOutstandingContinuationFee' => true,
                'signature' => [],
                'reference' => 'OLCS-12345',
                'isFinancialEvidenceRequired' => true,
            ],
            $this->sut->handleQuery($query)->serialize()
        );
    }

    public function testHandleQueryWithSignature()
    {
        $query = Qry::create(['id'=> 123]);

        $continuationDetail = m::mock(BundleSerializableInterface::class);
        $continuationDetail->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getOrganisation')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getType')->with()->andReturn(
                                m::mock()->shouldReceive('getId')->andReturn('ORG_TYPE_ID')->getMock()
                            )->once()
                        ->shouldReceive('getId')->with()->andReturn(99)->once()
                        ->getMock()
                    )
                    ->twice()
                    ->shouldReceive('getId')
                    ->andReturn(1)
                    ->twice()
                    ->getMock()
            )
            ->times(2)
            ->shouldReceive('serialize')
            ->with(['licence' => ['organisation', 'trafficArea']])
            ->andReturn(['licence_entity'])
            ->once()
            ->shouldReceive('getId')
            ->andReturn(123)
            ->once()
            ->shouldReceive('getAverageBalanceAmount')
            ->andReturn(0)
            ->once()
            ->shouldReceive('getFactoringAmount')
            ->andReturn(0)
            ->once()
            ->shouldReceive('getOverdraftAmount')
            ->andReturn(0)
            ->once()
            ->shouldReceive('getOtherFinancesAmount')
            ->andReturn(10000)
            ->once()
            ->getMock();

        $continuationDetail->shouldReceive('getDigitalSignature')->with()->times(4)->andReturn(
            m::mock()->shouldReceive('getSignatureName')->with()->once()->andReturn('NAME')
                ->shouldReceive('getCreatedOn')->with()->once()->andReturn('DATE')
                ->shouldReceive('getDateOfBirth')->with()->once()->andReturn('DOB')
                ->getMock()
        );

        $this->repoMap['ContinuationDetail']
            ->shouldReceive('fetchUsingId')->with($query)->once()->andReturn($continuationDetail);

        $this->repoMap['Document']
            ->shouldReceive('fetchListForContinuationDetail')->with(123, Query::HYDRATE_ARRAY)->once()
            ->andReturn(['document1', 'document2']);

        $this->repoMap['SystemParameter']
            ->shouldReceive('getDisableSelfServeCardPayments')
            ->andReturn(false)
            ->once();
        $this->repoMap['SystemParameter']
            ->shouldReceive('getDisableGdsVerifySignatures')
            ->andReturn('DISABLE_SIGNATURES')
            ->once();

        $mockFee = m::mock(BundleSerializableInterface::class)
            ->shouldReceive('serialize')
            ->with(['feeType' => ['feeType'], 'licence'])
            ->andReturn(['fee_entity'])
            ->once()
            ->getMock();

        $mockContinuationFee = m::mock()
            ->shouldReceive('getLatestTransaction')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getCompletedDate')
                    ->with(true)
                    ->andReturn((new DateTime('now'))->sub(new \DateInterval('P60D')))
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $this->repoMap['Fee']
            ->shouldReceive('fetchOutstandingContinuationFeesByLicenceId')
            ->with(1)
            ->andReturn([$mockFee])
            ->once()
            ->shouldReceive('fetchLatestPaidContinuationFee')
            ->with(1)
            ->andReturn($mockContinuationFee)
            ->once();

        $this->mockedSmServices['FinancialStandingHelperService']
            ->shouldReceive('getFinanceCalculationForOrganisation')->with(99)->once()->andReturn('123.99');

        $this->mockedSmServices['Review\ApplicationUndertakings']
            ->shouldReceive('getMarkupForLicence')->with($continuationDetail->getLicence())->once()
            ->andReturn('DECLARATIONS');

        $this->assertEquals(
            [
                'licence_entity',
                'financeRequired' => '123.99',
                'disableCardPayments' => false,
                'fees' => [
                    ['fee_entity']
                ],
                'documents' => ['document1', 'document2'],
                'organisationTypeId' => 'ORG_TYPE_ID',
                'declarations' => 'DECLARATIONS',
                'disableSignatures' => 'DISABLE_SIGNATURES',
                'hasOutstandingContinuationFee' => true,
                'signature' => [
                    'name' => 'NAME',
                    'date' => 'DATE',
                    'dob' => 'DOB',
                ],
                'reference' => null,
                'isFinancialEvidenceRequired' => false,
            ],
            $this->sut->handleQuery($query)->serialize()
        );
    }

    public function testHandleQueryWithSignatureContinuationFeeNotPaid()
    {
        $query = Qry::create(['id'=> 123]);

        $continuationDetail = m::mock(BundleSerializableInterface::class);
        $continuationDetail->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getOrganisation')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getType')->with()->andReturn(
                                m::mock()->shouldReceive('getId')->andReturn('ORG_TYPE_ID')->getMock()
                            )->once()
                            ->shouldReceive('getId')->with()->andReturn(99)->once()
                            ->getMock()
                    )
                    ->twice()
                    ->shouldReceive('getId')
                    ->andReturn(1)
                    ->twice()
                    ->getMock()
            )
            ->times(2)
            ->shouldReceive('serialize')
            ->with(['licence' => ['organisation', 'trafficArea']])
            ->andReturn(['licence_entity'])
            ->once()
            ->shouldReceive('getId')
            ->andReturn(123)
            ->once()
            ->shouldReceive('getAverageBalanceAmount')
            ->andReturn(0)
            ->once()
            ->shouldReceive('getFactoringAmount')
            ->andReturn(0)
            ->once()
            ->shouldReceive('getOverdraftAmount')
            ->andReturn(0)
            ->once()
            ->shouldReceive('getOtherFinancesAmount')
            ->andReturn(10000)
            ->once()
            ->getMock();

        $continuationDetail->shouldReceive('getDigitalSignature')->with()->times(4)->andReturn(
            m::mock()->shouldReceive('getSignatureName')->with()->once()->andReturn('NAME')
                ->shouldReceive('getCreatedOn')->with()->once()->andReturn('DATE')
                ->shouldReceive('getDateOfBirth')->with()->once()->andReturn('DOB')
                ->getMock()
        );

        $this->repoMap['ContinuationDetail']
            ->shouldReceive('fetchUsingId')->with($query)->once()->andReturn($continuationDetail);

        $this->repoMap['Document']
            ->shouldReceive('fetchListForContinuationDetail')->with(123, Query::HYDRATE_ARRAY)->once()
            ->andReturn(['document1', 'document2']);

        $this->repoMap['SystemParameter']
            ->shouldReceive('getDisableSelfServeCardPayments')
            ->andReturn(false)
            ->once();
        $this->repoMap['SystemParameter']
            ->shouldReceive('getDisableGdsVerifySignatures')
            ->andReturn('DISABLE_SIGNATURES')
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
            ->once()
            ->shouldReceive('fetchLatestPaidContinuationFee')
            ->with(1)
            ->andReturn(null)
            ->once();

        $this->mockedSmServices['FinancialStandingHelperService']
            ->shouldReceive('getFinanceCalculationForOrganisation')->with(99)->once()->andReturn('123.99');

        $this->mockedSmServices['Review\ApplicationUndertakings']
            ->shouldReceive('getMarkupForLicence')->with($continuationDetail->getLicence())->once()
            ->andReturn('DECLARATIONS');

        $this->assertEquals(
            [
                'licence_entity',
                'financeRequired' => '123.99',
                'disableCardPayments' => false,
                'fees' => [
                    ['fee_entity']
                ],
                'documents' => ['document1', 'document2'],
                'organisationTypeId' => 'ORG_TYPE_ID',
                'declarations' => 'DECLARATIONS',
                'disableSignatures' => 'DISABLE_SIGNATURES',
                'hasOutstandingContinuationFee' => true,
                'signature' => [
                    'name' => 'NAME',
                    'date' => 'DATE',
                    'dob' => 'DOB',
                ],
                'reference' => null,
                'isFinancialEvidenceRequired' => false,
            ],
            $this->sut->handleQuery($query)->serialize()
        );
    }
}
