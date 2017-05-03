<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\Summary;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query\Application\Summary as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * @covers Dvsa\Olcs\Api\Domain\QueryHandler\Application\Summary
 */
class SummaryTest extends QueryHandlerTestCase
{
    /** @var  Summary */
    protected $sut;

    /** @var m\MockInterface  */
    private $mockAppRepo;
    /** @var m\MockInterface  */
    private $mockFeeRepo;

    public function setUp()
    {
        $this->sut = new Summary();
        $this->mockRepo('Application', Repository\Application::class);
        $this->mockRepo('Fee', Repository\Fee::class);

        parent::setUp();

        $this->mockFeeRepo = $this->repoMap['Fee'];
        $this->mockAppRepo = $this->repoMap['Application'];

        $this->mockAppRepo
            ->shouldReceive('getCategoryReference')
            ->zeroOrMoreTimes()
            ->andReturnUsing(
                function ($category) {
                    return $category;
                }
            )
            ->shouldReceive('getSubCategoryReference')
            ->andReturnUsing(
                function ($category) {
                    return $category;
                }
            );
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $this->markTestSkipped();

        /** @var Entity\Application\Application|m\MockInterface $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->andReturn(['foo' => 'bar']);

        $adDocs1 = new ArrayCollection();
        $adDocs1->add(m::mock());

        $adDocs2 = new ArrayCollection();

        /** @var Entity\Application\ApplicationOperatingCentre|m\MockInterface $aoc1 */
        $aoc1 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc1->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs1);
        $aoc1->setAction('A');

        /** @var Entity\Application\ApplicationOperatingCentre|m\MockInterface $aoc2 */
        $aoc2 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc2->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs2);
        $aoc2->setAction('A');

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);
        $aocs->add($aoc2);

        $tm1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();

        $tms = new ArrayCollection();
        $tms->add($tm1);

        $mockApplication->setId(111);
        $mockApplication->setIsVariation(0);
        $mockApplication->setAuthSignature(0);
        $mockApplication->setOperatingCentres($aocs);
        $mockApplication->shouldReceive('getLicenceType->getId')
            ->andReturn(Entity\Licence\Licence::LICENCE_TYPE_STANDARD_NATIONAL);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);
        $mockApplication->setDocuments(new ArrayCollection());
        $mockApplication->shouldReceive('getLatestOutstandingApplicationFee')->andReturn(new \stdClass());

        $this->mockAppRepo->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

        $mockFee = m::mock()->shouldReceive('getLatestPaymentRef')->andReturn('ref')->once()->getMock();
        $this->mockFeeRepo->shouldReceive('fetchLatestPaidFeeByApplicationId')->with(111)->andReturn($mockFee)->once();

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'actions' => [
                    'PRINT_SIGN_RETURN' => 'PRINT_SIGN_RETURN',
                    'SUPPLY_SUPPORTING_EVIDENCE' => [
                        'MISSING_EVIDENCE_OC',
                        'markup-financial-standing-proof'
                    ],
                    'APPROVE_TM' => 'APPROVE_TM'
                ],
                'reference' => 'ref',
                'outstandingFee' => true,
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryDigitallySigned()
    {
        $query = Qry::create(['id' => 111]);

        $this->markTestSkipped();

        /** @var Entity\Application\Application|m\MockInterface $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('isDigitallySigned')->andReturn(true);
        $mockApplication->shouldReceive('serialize')->andReturn(['foo' => 'bar']);

        $adDocs1 = new ArrayCollection();
        $adDocs1->add(m::mock());

        $adDocs2 = new ArrayCollection();

        /** @var Entity\Application\ApplicationOperatingCentre|m\MockInterface $aoc1 */
        $aoc1 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc1->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs1);
        $aoc1->setAction('A');

        /** @var Entity\Application\ApplicationOperatingCentre|m\MockInterface $aoc2 */
        $aoc2 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc2->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs2);
        $aoc2->setAction('A');

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);
        $aocs->add($aoc2);

        $tm1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();

        $tms = new ArrayCollection();
        $tms->add($tm1);

        $mockApplication->setId(111);
        $mockApplication->setIsVariation(0);
        $mockApplication->setAuthSignature(0);
        $mockApplication->setOperatingCentres($aocs);
        $mockApplication->shouldReceive('getLicenceType->getId')
            ->andReturn(Entity\Licence\Licence::LICENCE_TYPE_STANDARD_NATIONAL);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);
        $mockApplication->setDocuments(new ArrayCollection());
        $mockApplication->shouldReceive('getLatestOutstandingApplicationFee')->andReturn(new \stdClass());

        $this->mockAppRepo->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

        $mockFee = m::mock()->shouldReceive('getLatestPaymentRef')->andReturn('ref')->once()->getMock();
        $this->mockFeeRepo->shouldReceive('fetchLatestPaidFeeByApplicationId')->with(111)->andReturn($mockFee)->once();

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'actions' => [
                    'SUPPLY_SUPPORTING_EVIDENCE' => [
                        'MISSING_EVIDENCE_OC',
                        'markup-financial-standing-proof'
                    ],
                    'APPROVE_TM' => 'APPROVE_TM'
                ],
                'reference' => 'ref',
                'outstandingFee' => true,
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryWithDocs()
    {
        $query = Qry::create(['id' => 111]);

        $this->markTestSkipped();

        /** @var Entity\Application\Application|m\MockInterface $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->andReturn(['foo' => 'bar']);

        $adDocs1 = new ArrayCollection();
        $adDocs1->add(m::mock());

        $adDocs2 = new ArrayCollection();

        /** @var Entity\Application\ApplicationOperatingCentre|m\MockInterface $aoc1 */
        $aoc1 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc1->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs1);
        $aoc1->setAction('A');

        /** @var Entity\Application\ApplicationOperatingCentre|m\MockInterface $aoc2 */
        $aoc2 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc2->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs2);
        $aoc2->setAction('A');

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);
        $aocs->add($aoc2);

        $tm1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();

        $tms = new ArrayCollection();
        $tms->add($tm1);

        $mockApplication->setId(111);
        $mockApplication->setIsVariation(0);
        $mockApplication->setAuthSignature(0);
        $mockApplication->setOperatingCentres($aocs);
        $mockApplication->shouldReceive('getLicenceType->getId')
            ->andReturn(Entity\Licence\Licence::LICENCE_TYPE_STANDARD_NATIONAL);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);
        $mockApplication->shouldReceive('getLatestOutstandingApplicationFee')->andReturn(null);

        $docs = new ArrayCollection();
        $docs->add(m::mock());

        $mockApplication->shouldReceive('getApplicationDocuments')
            ->with(
                Entity\System\Category::CATEGORY_APPLICATION,
                Entity\System\Category::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL
            )
            ->andReturn($docs);

        $this->mockAppRepo->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

        $this->mockFeeRepo->shouldReceive('fetchLatestPaidFeeByApplicationId')->with(111)->andReturn([])->once();

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'actions' => [
                    'PRINT_SIGN_RETURN' => 'PRINT_SIGN_RETURN',
                    'SUPPLY_SUPPORTING_EVIDENCE' => [
                        'MISSING_EVIDENCE_OC'
                    ],
                    'APPROVE_TM' => 'APPROVE_TM'
                ],
                'reference' => '',
                'outstandingFee' => false,
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryWithPsv()
    {
        $query = Qry::create(['id' => 111]);

        $this->markTestSkipped();

        /** @var Entity\Application\Application|m\MockInterface $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->andReturn(['foo' => 'bar']);
        $mockApplication->shouldReceive('isPsv')->andReturn(true);

        $tm1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();

        $tms = new ArrayCollection();
        $tms->add($tm1);

        $mockApplication->setId(111);
        $mockApplication->setIsVariation(0);
        $mockApplication->setAuthSignature(0);
        $mockApplication->shouldReceive('getLicenceType->getId')
            ->andReturn(Entity\Licence\Licence::LICENCE_TYPE_STANDARD_NATIONAL);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);
        $mockApplication->shouldReceive('getLatestOutstandingApplicationFee')->andReturn(null);

        $docs = new ArrayCollection();
        $docs->add(m::mock());

        $mockApplication->shouldReceive('getApplicationDocuments')
            ->with(
                Entity\System\Category::CATEGORY_APPLICATION,
                Entity\System\Category::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL
            )
            ->andReturn($docs);

        $this->mockAppRepo->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

        $this->mockFeeRepo->shouldReceive('fetchLatestPaidFeeByApplicationId')->with(111)->andReturn([])->once();

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'actions' => [
                    'PRINT_SIGN_RETURN' => 'PRINT_SIGN_RETURN',
                    'APPROVE_TM' => 'APPROVE_TM'
                ],
                'reference' => '',
                'outstandingFee' => false,
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryWithDocs2()
    {
        $query = Qry::create(['id' => 111]);

        $this->markTestSkipped();

        /** @var Entity\Application\Application|m\MockInterface $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->andReturn(['foo' => 'bar']);

        $adDocs1 = new ArrayCollection();
        $adDocs1->add(m::mock());

        $adDocs2 = new ArrayCollection();

        /** @var Entity\Application\ApplicationOperatingCentre|m\MockInterface $aoc1 */
        $aoc1 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc1->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs1);
        $aoc1->setAction('A');

        /** @var Entity\Application\ApplicationOperatingCentre|m\MockInterface $aoc2 */
        $aoc2 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc2->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs2);
        $aoc2->setAction('A');

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);
        $aocs->add($aoc2);

        $tm1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();

        $tms = new ArrayCollection();
        $tms->add($tm1);

        $mockApplication->setId(111);
        $mockApplication->setIsVariation(0);
        $mockApplication->setAuthSignature(0);
        $mockApplication->setOperatingCentres($aocs);
        $mockApplication->shouldReceive('getLicenceType->getId')
            ->andReturn(Entity\Licence\Licence::LICENCE_TYPE_STANDARD_NATIONAL);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);
        $mockApplication->shouldReceive('getLatestOutstandingApplicationFee')->andReturn(null);

        $docs = new ArrayCollection();
        $docs->add(m::mock());

        $mockApplication->shouldReceive('getApplicationDocuments')
            ->with(
                Entity\System\Category::CATEGORY_APPLICATION,
                Entity\System\Category::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL
            )
            ->andReturn(new ArrayCollection());

        $mockApplication->shouldReceive('getApplicationDocuments')
            ->with(
                Entity\System\Category::CATEGORY_APPLICATION,
                Entity\System\Category::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_ASSISTED_DIGITAL
            )
            ->andReturn($docs);

        $this->mockAppRepo->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

        $mockFee = m::mock()->shouldReceive('getLatestPaymentRef')->andReturn('ref')->once()->getMock();
        $this->mockFeeRepo->shouldReceive('fetchLatestPaidFeeByApplicationId')->with(111)->andReturn($mockFee)->once();

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'actions' => [
                    'PRINT_SIGN_RETURN' => 'PRINT_SIGN_RETURN',
                    'SUPPLY_SUPPORTING_EVIDENCE' => [
                        'MISSING_EVIDENCE_OC'
                    ],
                    'APPROVE_TM' => 'APPROVE_TM'
                ],
                'reference' => 'ref',
                'outstandingFee' => false,
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryNoNeedToSign()
    {
        $query = Qry::create(['id' => 111]);

        $this->markTestSkipped();

        /** @var Entity\Application\Application|m\MockInterface $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->andReturn(['foo' => 'bar']);

        $adDocs = new ArrayCollection();

        /** @var Entity\Application\ApplicationOperatingCentre|m\MockInterface $aoc1 */
        $aoc1 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc1->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs);
        $aoc1->setAction('A');

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);

        $tm1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();

        $tms = new ArrayCollection();
        $tms->add($tm1);

        $mockApplication->setId(111);
        $mockApplication->setIsVariation(0);
        $mockApplication->setAuthSignature(1);
        $mockApplication->setOperatingCentres($aocs);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);
        $mockApplication->shouldReceive('getLicenceType->getId')
            ->andReturn(Entity\Licence\Licence::LICENCE_TYPE_STANDARD_NATIONAL);
        $mockApplication->setDocuments(new ArrayCollection());
        $mockApplication->shouldReceive('getLatestOutstandingApplicationFee')->andReturn(null);

        $this->mockAppRepo->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

        $mockFee = m::mock()->shouldReceive('getLatestPaymentRef')->andReturn('ref')->once()->getMock();
        $this->mockFeeRepo->shouldReceive('fetchLatestPaidFeeByApplicationId')->with(111)->andReturn($mockFee)->once();

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'actions' => [
                    'SUPPLY_SUPPORTING_EVIDENCE' => [
                        'MISSING_EVIDENCE_OC',
                        'markup-financial-standing-proof'
                    ],
                    'APPROVE_TM' => 'APPROVE_TM'
                ],
                'reference' => 'ref',
                'outstandingFee' => false,
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryVariation()
    {
        $query = Qry::create(['id' => 111]);

        $this->markTestSkipped();

        /** @var Entity\Application\Application|m\MockInterface $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->andReturn(['foo' => 'bar']);
        $mockApplication->shouldReceive('getApplicationCompletion->getFinancialEvidenceStatus')
            ->andReturn(Entity\Application\Application::VARIATION_STATUS_UPDATED);

        $adDocs = new ArrayCollection();

        /** @var Entity\Application\ApplicationOperatingCentre|m\MockInterface $aoc1 */
        $aoc1 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc1->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs);
        $aoc1->setAction('A');

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);

        $tm1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();

        $tms = new ArrayCollection();
        $tms->add($tm1);

        $mockApplication->setId(111);
        $mockApplication->setIsVariation(1);
        $mockApplication->setAuthSignature(1);
        $mockApplication->setOperatingCentres($aocs);
        $mockApplication->shouldReceive('getLicenceType->getId')
            ->andReturn(Entity\Licence\Licence::LICENCE_TYPE_STANDARD_NATIONAL);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);
        $mockApplication->setDocuments(new ArrayCollection());
        $mockApplication->shouldReceive('getLatestOutstandingApplicationFee')->andReturn(null);

        $this->mockAppRepo->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

        $mockFee = m::mock()->shouldReceive('getLatestPaymentRef')->andReturn('ref')->once()->getMock();
        $this->mockFeeRepo->shouldReceive('fetchLatestPaidFeeByApplicationId')->with(111)->andReturn($mockFee)->once();

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'actions' => [
                    'SUPPLY_SUPPORTING_EVIDENCE' => [
                        'MISSING_EVIDENCE_OC',
                        'markup-financial-standing-proof'
                    ],
                    'APPROVE_TM' => 'APPROVE_TM'
                ],
                'reference' => 'ref',
                'outstandingFee' => false,
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryVariationUnchangedFinancialEvidence()
    {
        $query = Qry::create(['id' => 111]);

        $this->markTestSkipped();

        /** @var Entity\Application\Application|m\MockInterface $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->andReturn(['foo' => 'bar']);
        $mockApplication->shouldReceive('getApplicationCompletion->getFinancialEvidenceStatus')
            ->andReturn(Entity\Application\Application::VARIATION_STATUS_UNCHANGED);

        $adDocs = new ArrayCollection();

        /** @var Entity\Application\ApplicationOperatingCentre|m\MockInterface $aoc1 */
        $aoc1 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc1->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs);
        $aoc1->setAction('A');

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);

        $tm1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();

        $tms = new ArrayCollection();
        $tms->add($tm1);

        $mockApplication->setId(111);
        $mockApplication->setIsVariation(1);
        $mockApplication->setAuthSignature(1);
        $mockApplication->setOperatingCentres($aocs);
        $mockApplication->shouldReceive('getLicenceType->getId')
            ->andReturn(Entity\Licence\Licence::LICENCE_TYPE_STANDARD_NATIONAL);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);
        $mockApplication->setDocuments(new ArrayCollection());
        $mockApplication->shouldReceive('getLatestOutstandingApplicationFee')->andReturn(null);

        $mockFee = m::mock()->shouldReceive('getLatestPaymentRef')->andReturn('ref')->once()->getMock();
        $this->mockFeeRepo->shouldReceive('fetchLatestPaidFeeByApplicationId')->with(111)->andReturn($mockFee)->once();

        $this->mockAppRepo->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'actions' => [
                    'SUPPLY_SUPPORTING_EVIDENCE' => [
                        'MISSING_EVIDENCE_OC'
                    ],
                    'APPROVE_TM' => 'APPROVE_TM'
                ],
                'reference' => 'ref',
                'outstandingFee' => false,
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryVariationUpdatedOcWithIncrease()
    {
        $query = Qry::create(['id' => 111]);

        $this->markTestSkipped();

        /** @var Entity\Application\Application|m\MockInterface $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->andReturn(['foo' => 'bar']);
        $mockApplication->shouldReceive('getApplicationCompletion->getFinancialEvidenceStatus')
            ->andReturn(Entity\Application\Application::VARIATION_STATUS_UNCHANGED);

        /** @var Entity\Licence\LicenceOperatingCentre $loc */
        $loc = m::mock(Entity\Licence\LicenceOperatingCentre::class)->makePartial();
        $loc->setNoOfVehiclesRequired(10);

        $mockApplication->shouldReceive('getLicence->getLocByOc')->andReturn($loc);

        $adDocs = new ArrayCollection();

        /** @var Entity\Application\ApplicationOperatingCentre|m\MockInterface $aoc1 */
        $aoc1 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc1->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs);
        $aoc1->setAction('U');
        $aoc1->setNoOfVehiclesRequired(11);

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);

        $tm1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();

        $tms = new ArrayCollection();
        $tms->add($tm1);

        $mockApplication->setId(111);
        $mockApplication->setIsVariation(1);
        $mockApplication->setAuthSignature(1);
        $mockApplication->setOperatingCentres($aocs);
        $mockApplication->shouldReceive('getLicenceType->getId')
            ->andReturn(Entity\Licence\Licence::LICENCE_TYPE_STANDARD_NATIONAL);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);
        $mockApplication->setDocuments(new ArrayCollection());
        $mockApplication->shouldReceive('getLatestOutstandingApplicationFee')->andReturn(null);

        $this->mockAppRepo->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

        $mockFee = m::mock()->shouldReceive('getLatestPaymentRef')->andReturn('ref')->once()->getMock();
        $this->mockFeeRepo->shouldReceive('fetchLatestPaidFeeByApplicationId')->with(111)->andReturn($mockFee)->once();

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'actions' => [
                    'SUPPLY_SUPPORTING_EVIDENCE' => [
                        'MISSING_EVIDENCE_OC'
                    ],
                    'APPROVE_TM' => 'APPROVE_TM'
                ],
                'reference' => 'ref',
                'outstandingFee' => false,
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryVariationUpdatedOcWithoutIncrease()
    {
        $query = Qry::create(['id' => 111]);

        $this->markTestSkipped();

        /** @var Entity\Application\Application|m\MockInterface $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->andReturn(['foo' => 'bar']);
        $mockApplication->shouldReceive('getApplicationCompletion->getFinancialEvidenceStatus')
            ->andReturn(Entity\Application\Application::VARIATION_STATUS_UNCHANGED);

        /** @var Entity\Licence\LicenceOperatingCentre $loc */
        $loc = m::mock(Entity\Licence\LicenceOperatingCentre::class)->makePartial();
        $loc->setNoOfVehiclesRequired(10);
        $loc->setNoOfTrailersRequired(10);

        $mockApplication->shouldReceive('getLicence->getLocByOc')->andReturn($loc);

        $adDocs = new ArrayCollection();

        /** @var Entity\Application\ApplicationOperatingCentre|m\MockInterface $aoc1 */
        $aoc1 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc1->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs);
        $aoc1->setAction('U');
        $aoc1->setNoOfVehiclesRequired(10);
        $aoc1->setNoOfTrailersRequired(10);

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);

        $tm1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();

        $tms = new ArrayCollection();
        $tms->add($tm1);

        $mockApplication->setId(111);
        $mockApplication->setIsVariation(1);
        $mockApplication->setAuthSignature(1);
        $mockApplication->setOperatingCentres($aocs);
        $mockApplication->shouldReceive('getLicenceType->getId')
            ->andReturn(Entity\Licence\Licence::LICENCE_TYPE_STANDARD_NATIONAL);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);
        $mockApplication->setDocuments(new ArrayCollection());
        $mockApplication->shouldReceive('getLatestOutstandingApplicationFee')->andReturn(null);

        $this->mockAppRepo->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

        $mockFee = m::mock()->shouldReceive('getLatestPaymentRef')->andReturn('ref')->once()->getMock();
        $this->mockFeeRepo->shouldReceive('fetchLatestPaidFeeByApplicationId')->with(111)->andReturn($mockFee)->once();

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'actions' => [
                    'APPROVE_TM' => 'APPROVE_TM'
                ],
                'reference' => 'ref',
                'outstandingFee' => false,
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryVariationWithNoApplicationData()
    {
        $query = Qry::create(['id' => 111]);

        $this->markTestSkipped();

        /** @var Entity\Application\Application|m\MockInterface $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->with(['licence', 'status'])->andReturn(['foo' => 'bar']);
        $mockApplication->shouldReceive('getApplicationCompletion->getFinancialEvidenceStatus')
            ->andReturn(Entity\Application\Application::VARIATION_STATUS_UNCHANGED);

        $mockApplication->setId(111);
        $mockApplication->setIsVariation(1);
        $mockApplication->shouldReceive('getLicenceType->getId')
            ->andReturn(Entity\Licence\Licence::LICENCE_TYPE_STANDARD_NATIONAL);
        $mockApplication->setOperatingCentres(new ArrayCollection());
        $mockApplication->setTransportManagers(new ArrayCollection());
        $mockApplication->setDocuments(new ArrayCollection());
        $mockApplication->shouldReceive('getLatestOutstandingApplicationFee')->andReturn(null);

        $this->mockAppRepo->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

        $mockFee = m::mock()->shouldReceive('getLatestPaymentRef')->andReturn('ref')->once()->getMock();
        $this->mockFeeRepo->shouldReceive('fetchLatestPaidFeeByApplicationId')->with(111)->andReturn($mockFee)->once();

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'actions' => [],
                'reference' => 'ref',
                'outstandingFee' => false,
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryWisthSpecialRestrictedLicence()
    {
        $query = Qry::create(['id' => 111]);

        $this->markTestSkipped();

        /** @var Entity\Application\Application|m\MockInterface $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->andReturn(['foo' => 'bar']);

        $adDocs1 = new ArrayCollection();
        $adDocs1->add(m::mock());

        $adDocs2 = new ArrayCollection();

        /** @var Entity\Application\ApplicationOperatingCentre|m\MockInterface $aoc1 */
        $aoc1 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc1->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs1);
        $aoc1->setAction('A');

        /** @var Entity\Application\ApplicationOperatingCentre|m\MockInterface $aoc2 */
        $aoc2 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc2->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs2);
        $aoc2->setAction('A');

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);
        $aocs->add($aoc2);

        $tm1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();

        $tms = new ArrayCollection();
        $tms->add($tm1);

        $mockApplication->setId(111);
        $mockApplication->setIsVariation(0);
        $mockApplication->setAuthSignature(0);
        $mockApplication->setOperatingCentres($aocs);
        $mockApplication->shouldReceive('getLicenceType->getId')
            ->andReturn(Entity\Licence\Licence::LICENCE_TYPE_SPECIAL_RESTRICTED);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);
        $mockApplication->setDocuments(new ArrayCollection());
        $mockApplication->shouldReceive('getLatestOutstandingApplicationFee')->andReturn(new \stdClass());

        $this->mockAppRepo->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

        $mockFee = m::mock()->shouldReceive('getLatestPaymentRef')->andReturn('ref')->once()->getMock();
        $this->mockFeeRepo->shouldReceive('fetchLatestPaidFeeByApplicationId')->with(111)->andReturn($mockFee)->once();

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'actions' => [
                    'PRINT_SIGN_RETURN' => 'PRINT_SIGN_RETURN'
                ],
                'reference' => 'ref',
                'outstandingFee' => true,
            ],
            $result->serialize()
        );
    }
}
