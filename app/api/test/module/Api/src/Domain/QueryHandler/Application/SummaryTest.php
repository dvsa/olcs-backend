<?php

/**
 * Summary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\Summary;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query\Application\Summary as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Summary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SummaryTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Summary();
        $this->mockRepo('Application', Repository\Application::class);

        parent::setUp();

        $this->repoMap['Application']->shouldReceive('getCategoryReference')
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

        /** @var Entity\Application\Application $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->andReturn(['foo' => 'bar']);

        $adDocs1 = new ArrayCollection();
        $adDocs1->add(m::mock());

        $adDocs2 = new ArrayCollection();

        $aoc1 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc1->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs1);
        $aoc1->setAction('A');

        $aoc2 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc2->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs2);
        $aoc2->setAction('A');

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);
        $aocs->add($aoc2);

        $tm1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();

        $tms = new ArrayCollection();
        $tms->add($tm1);

        $mockApplication->setIsVariation(0);
        $mockApplication->setAuthSignature(0);
        $mockApplication->setOperatingCentres($aocs);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);
        $mockApplication->setDocuments(new ArrayCollection());

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'actions' => [
                    'PRINT_SIGN_RETURN' => 'PRINT_SIGN_RETURN',
                    'SUPPLY_SUPPORTING_EVIDENCE' => [
                        'MISSING_EVIDENCE_OC',
                        'MISSING_EVIDENCE_FINANCIAL'
                    ],
                    'APPROVE_TM' => 'APPROVE_TM'
                ]
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryWithDocs()
    {
        $query = Qry::create(['id' => 111]);

        /** @var Entity\Application\Application $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->andReturn(['foo' => 'bar']);

        $adDocs1 = new ArrayCollection();
        $adDocs1->add(m::mock());

        $adDocs2 = new ArrayCollection();

        $aoc1 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc1->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs1);
        $aoc1->setAction('A');

        $aoc2 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc2->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs2);
        $aoc2->setAction('A');

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);
        $aocs->add($aoc2);

        $tm1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();

        $tms = new ArrayCollection();
        $tms->add($tm1);

        $mockApplication->setIsVariation(0);
        $mockApplication->setAuthSignature(0);
        $mockApplication->setOperatingCentres($aocs);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);

        $docs = new ArrayCollection();
        $docs->add(m::mock());

        $mockApplication->shouldReceive('getApplicationDocuments')
            ->with(
                Entity\System\Category::CATEGORY_APPLICATION,
                Entity\System\Category::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL
            )
            ->andReturn($docs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

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
                ]
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryWithDocs2()
    {
        $query = Qry::create(['id' => 111]);

        /** @var Entity\Application\Application $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->andReturn(['foo' => 'bar']);

        $adDocs1 = new ArrayCollection();
        $adDocs1->add(m::mock());

        $adDocs2 = new ArrayCollection();

        $aoc1 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc1->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs1);
        $aoc1->setAction('A');

        $aoc2 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc2->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs2);
        $aoc2->setAction('A');

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);
        $aocs->add($aoc2);

        $tm1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();

        $tms = new ArrayCollection();
        $tms->add($tm1);

        $mockApplication->setIsVariation(0);
        $mockApplication->setAuthSignature(0);
        $mockApplication->setOperatingCentres($aocs);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);

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

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

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
                ]
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryNoNeedToSign()
    {
        $query = Qry::create(['id' => 111]);

        /** @var Entity\Application\Application $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->andReturn(['foo' => 'bar']);

        $adDocs = new ArrayCollection();

        $aoc1 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc1->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs);
        $aoc1->setAction('A');

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);

        $tm1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();

        $tms = new ArrayCollection();
        $tms->add($tm1);

        $mockApplication->setIsVariation(0);
        $mockApplication->setAuthSignature(1);
        $mockApplication->setOperatingCentres($aocs);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);
        $mockApplication->setDocuments(new ArrayCollection());

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'actions' => [
                    'SUPPLY_SUPPORTING_EVIDENCE' => [
                        'MISSING_EVIDENCE_OC',
                        'MISSING_EVIDENCE_FINANCIAL'
                    ],
                    'APPROVE_TM' => 'APPROVE_TM'
                ]
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryVariation()
    {
        $query = Qry::create(['id' => 111]);

        /** @var Entity\Application\Application $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->andReturn(['foo' => 'bar']);
        $mockApplication->shouldReceive('getApplicationCompletion->getFinancialEvidenceStatus')
            ->andReturn(Entity\Application\Application::VARIATION_STATUS_UPDATED);

        $adDocs = new ArrayCollection();

        $aoc1 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc1->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs);
        $aoc1->setAction('A');

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);

        $tm1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();

        $tms = new ArrayCollection();
        $tms->add($tm1);

        $mockApplication->setIsVariation(1);
        $mockApplication->setAuthSignature(1);
        $mockApplication->setOperatingCentres($aocs);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);
        $mockApplication->setDocuments(new ArrayCollection());

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'actions' => [
                    'SUPPLY_SUPPORTING_EVIDENCE' => [
                        'MISSING_EVIDENCE_OC',
                        'MISSING_EVIDENCE_FINANCIAL'
                    ],
                    'APPROVE_TM' => 'APPROVE_TM'
                ]
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryVariationUnchangedFinancialEvidence()
    {
        $query = Qry::create(['id' => 111]);

        /** @var Entity\Application\Application $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->andReturn(['foo' => 'bar']);
        $mockApplication->shouldReceive('getApplicationCompletion->getFinancialEvidenceStatus')
            ->andReturn(Entity\Application\Application::VARIATION_STATUS_UNCHANGED);

        $adDocs = new ArrayCollection();

        $aoc1 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc1->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs);
        $aoc1->setAction('A');

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);

        $tm1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();

        $tms = new ArrayCollection();
        $tms->add($tm1);

        $mockApplication->setIsVariation(1);
        $mockApplication->setAuthSignature(1);
        $mockApplication->setOperatingCentres($aocs);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);
        $mockApplication->setDocuments(new ArrayCollection());

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
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
                ]
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryVariationUpdatedOcWithIncrease()
    {
        $query = Qry::create(['id' => 111]);

        /** @var Entity\Application\Application $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->andReturn(['foo' => 'bar']);
        $mockApplication->shouldReceive('getApplicationCompletion->getFinancialEvidenceStatus')
            ->andReturn(Entity\Application\Application::VARIATION_STATUS_UNCHANGED);

        /** @var Entity\Licence\LicenceOperatingCentre $loc */
        $loc = m::mock(Entity\Licence\LicenceOperatingCentre::class)->makePartial();
        $loc->setNoOfVehiclesRequired(10);

        $mockApplication->shouldReceive('getLicence->getLocByOc')->andReturn($loc);

        $adDocs = new ArrayCollection();

        $aoc1 = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc1->shouldReceive('getOperatingCentre->getAdDocuments->matching')->andReturn($adDocs);
        $aoc1->setAction('U');
        $aoc1->setNoOfVehiclesRequired(11);

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);

        $tm1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();

        $tms = new ArrayCollection();
        $tms->add($tm1);

        $mockApplication->setIsVariation(1);
        $mockApplication->setAuthSignature(1);
        $mockApplication->setOperatingCentres($aocs);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);
        $mockApplication->setDocuments(new ArrayCollection());

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
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
                ]
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryVariationUpdatedOcWithoutIncrease()
    {
        $query = Qry::create(['id' => 111]);

        /** @var Entity\Application\Application $mockApplication */
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

        $mockApplication->setIsVariation(1);
        $mockApplication->setAuthSignature(1);
        $mockApplication->setOperatingCentres($aocs);
        $mockApplication->shouldReceive('getTransportManagers->matching')->andReturn($tms);
        $mockApplication->setDocuments(new ArrayCollection());

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'actions' => [
                    'APPROVE_TM' => 'APPROVE_TM'
                ]
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryVariationWithNoApplicationData()
    {
        $query = Qry::create(['id' => 111]);

        /** @var Entity\Application\Application $mockApplication */
        $mockApplication = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApplication->shouldReceive('serialize')->with(['licence', 'status'])->andReturn(['foo' => 'bar']);
        $mockApplication->shouldReceive('getApplicationCompletion->getFinancialEvidenceStatus')
            ->andReturn(Entity\Application\Application::VARIATION_STATUS_UNCHANGED);

        $mockApplication->setIsVariation(1);
        $mockApplication->setOperatingCentres(new ArrayCollection());
        $mockApplication->setTransportManagers(new ArrayCollection());
        $mockApplication->setDocuments(new ArrayCollection());

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($mockApplication);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'actions' => []
            ],
            $result->serialize()
        );
    }
}
