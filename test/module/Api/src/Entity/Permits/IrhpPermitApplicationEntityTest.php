<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Mockery as m;

/**
 * IrhpPermitApplication Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class IrhpPermitApplicationEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * @var Entity
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Entity();

        parent::setUp();
    }

    public function testCreateNew()
    {
        $irhpPermitWindow = m::mock(IrhpPermitWindow::class);
        $licence = m::mock(Licence::class);
        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);

        $irhpPermitApplication = Entity::createNew(
            $irhpPermitWindow,
            $licence,
            $ecmtPermitApplication
        );

        $this->assertSame($irhpPermitWindow, $irhpPermitApplication->getIrhpPermitWindow());
        $this->assertSame($licence, $irhpPermitApplication->getLicence());
        $this->assertSame($ecmtPermitApplication, $irhpPermitApplication->getEcmtPermitApplication());
        $this->assertNull($irhpPermitApplication->getIrhpApplication());
    }

    public function testCreateNewForIrhpApplication()
    {
        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpPermitWindow = m::mock(IrhpPermitWindow::class);

        $irhpPermitApplication = Entity::createNewForIrhpApplication(
            $irhpApplication,
            $irhpPermitWindow
        );

        $this->assertSame($irhpApplication, $irhpPermitApplication->getIrhpApplication());
        $this->assertSame($irhpPermitWindow, $irhpPermitApplication->getIrhpPermitWindow());
        $this->assertNull($irhpPermitApplication->getEcmtPermitApplication());
        $this->assertNull($irhpPermitApplication->getLicence());
    }

    /**
     * @dataProvider dpEmissionsCategoriesAndNull
     */
    public function testGetPermitIntensityOfUse($emissionsCategoryId)
    {
        $intensityOfUse = 0.75;

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('getPermitIntensityOfUse')
            ->with($emissionsCategoryId)
            ->once()
            ->andReturn($intensityOfUse);

        $irhpPermitApplication = m::mock(Entity::class)->makePartial();
        $irhpPermitApplication->setEcmtPermitApplication($ecmtPermitApplication);

        $this->assertEquals(
            $intensityOfUse,
            $irhpPermitApplication->getPermitIntensityOfUse($emissionsCategoryId)
        );
    }

    /**
     * @dataProvider dpEmissionsCategoriesAndNull
     */
    public function testGetPermitApplicationScore($emissionsCategoryId)
    {
        $applicationScore = 0.75;

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('getPermitApplicationScore')
            ->with($emissionsCategoryId)
            ->once()
            ->andReturn($applicationScore);

        $irhpPermitApplication = m::mock(Entity::class)->makePartial();
        $irhpPermitApplication->setEcmtPermitApplication($ecmtPermitApplication);

        $this->assertEquals(
            $applicationScore,
            $irhpPermitApplication->getPermitApplicationScore($emissionsCategoryId)
        );
    }

    public function dpEmissionsCategoriesAndNull()
    {
        return [
            [RefData::EMISSIONS_CATEGORY_EURO5_REF],
            [RefData::EMISSIONS_CATEGORY_EURO6_REF],
            [null],
        ];
    }

    public function testGetCalculatedBundleValues()
    {
        $this->assertSame(
            [
                'permitsAwarded' => 0,
                'euro5PermitsAwarded' => 0,
                'euro6PermitsAwarded' => 0,
                'validPermits' => 0,
                'relatedApplication' => null,
            ],
            $this->sut->getCalculatedBundleValues()
        );
    }

    public function testGetCalculatedBundleValuesForEcmtPermitApplication()
    {
        $irhpPermitWindow = m::mock(IrhpPermitWindow::class);
        $licence = m::mock(Licence::class);
        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('serialize')
            ->with(
                [
                    'licence' => [
                        'organisation'
                    ]
                ]
            )
            ->once()
            ->andReturn(['EcmtPermitApplication']);

        $irhpPermitApplication = Entity::createNew(
            $irhpPermitWindow,
            $licence,
            $ecmtPermitApplication
        );

        $this->assertSame(
            [
                'permitsAwarded' => 0,
                'euro5PermitsAwarded' => 0,
                'euro6PermitsAwarded' => 0,
                'validPermits' => 0,
                'relatedApplication' => ['EcmtPermitApplication'],
            ],
            $irhpPermitApplication->getCalculatedBundleValues()
        );
    }

    public function testGetCalculatedBundleValuesForIrhpApplication()
    {
        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('serialize')
            ->with(
                [
                    'licence' => [
                        'organisation'
                    ]
                ]
            )
            ->once()
            ->andReturn(['IrhpApplication']);
        $irhpPermitWindow = m::mock(IrhpPermitWindow::class);

        $irhpPermitApplication = Entity::createNewForIrhpApplication(
            $irhpApplication,
            $irhpPermitWindow
        );

        $this->assertSame(
            [
                'permitsAwarded' => 0,
                'euro5PermitsAwarded' => 0,
                'euro6PermitsAwarded' => 0,
                'validPermits' => 0,
                'relatedApplication' => ['IrhpApplication'],
            ],
            $irhpPermitApplication->getCalculatedBundleValues()
        );
    }

    /**
     * @dataProvider dpCountValidPermits
     */
    public function testCountValidPermits($statusId, $count)
    {
        $irhpPermitRange = m::mock(IrhpPermitRange::class);
        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);

        $irhpCandidate = m::mock(IrhpCandidatePermit::class);
        $irhpCandidate->shouldReceive('getIrhpPermitRange')
            ->once()
            ->withNoArgs()
            ->andReturn($irhpPermitRange);
        $irhpCandidate->shouldReceive('getIrhpPermitApplication')
            ->once()
            ->withNoArgs()
            ->andReturn($irhpPermitApplication);

        $issueDate = new \DateTime();
        $permitNumber = '00001';
        $status = new RefData($statusId);

        $irhpPermit = IrhpPermit::createNew(
            $irhpCandidate,
            $issueDate,
            $status,
            $permitNumber
        );

        $collection = new ArrayCollection([$irhpPermit]);

        $this->sut->setIrhpPermits($collection);
        $this->assertEquals($count, $this->sut->countValidPermits());
    }

    public function dpCountValidPermits()
    {
        return [
            [IrhpPermit::STATUS_PENDING, 1],
            [IrhpPermit::STATUS_AWAITING_PRINTING, 1],
            [IrhpPermit::STATUS_PRINTING, 1],
            [IrhpPermit::STATUS_PRINTED, 1],
            [IrhpPermit::STATUS_ERROR, 1],
            [IrhpPermit::STATUS_CEASED, 0],
            [IrhpPermit::STATUS_TERMINATED, 0]
        ];
    }

    /**
     * @dataProvider dpTestCountPermitsAwarded
     */
    public function testCountPermitsAwarded($assignedEmissionsCategoryId, $expectedCount)
    {
        $irhpCandidatePermits = $this->getPermitsAwardedMocks();
        foreach ($irhpCandidatePermits as $irhpCandidatePermit) {
            $this->sut->addIrhpCandidatePermits($irhpCandidatePermit);
        }

        $this->assertEquals(
            $expectedCount,
            $this->sut->countPermitsAwarded($assignedEmissionsCategoryId)
        );
    }

    public function dpTestCountPermitsAwarded()
    {
        return [
            [null, 3],
            [RefData::EMISSIONS_CATEGORY_EURO5_REF, 2],
            [RefData::EMISSIONS_CATEGORY_EURO6_REF, 1],
        ];
    }

    public function testGetSuccessfulIrhpCandidatePermitsNoEmissionsCategory()
    {
        $irhpCandidatePermits = $this->getPermitsAwardedMocks();
        foreach ($irhpCandidatePermits as $irhpCandidatePermit) {
            $this->sut->addIrhpCandidatePermits($irhpCandidatePermit);
        }

        $successfulIrhpCandidatePermits = $this->sut->getSuccessfulIrhpCandidatePermits();

        $this->assertTrue($successfulIrhpCandidatePermits->contains($irhpCandidatePermits[0]));
        $this->assertTrue($successfulIrhpCandidatePermits->contains($irhpCandidatePermits[2]));
        $this->assertTrue($successfulIrhpCandidatePermits->contains($irhpCandidatePermits[4]));
        $this->assertEquals(3, $successfulIrhpCandidatePermits->count());
    }

    public function testGetSuccessfulIrhpCandidatePermitsEuro5EmissionsCategory()
    {
        $irhpCandidatePermits = $this->getPermitsAwardedMocks();
        foreach ($irhpCandidatePermits as $irhpCandidatePermit) {
            $this->sut->addIrhpCandidatePermits($irhpCandidatePermit);
        }

        $successfulIrhpCandidatePermits = $this->sut->getSuccessfulIrhpCandidatePermits(
            RefData::EMISSIONS_CATEGORY_EURO5_REF
        );

        $this->assertTrue($successfulIrhpCandidatePermits->contains($irhpCandidatePermits[0]));
        $this->assertTrue($successfulIrhpCandidatePermits->contains($irhpCandidatePermits[2]));
        $this->assertEquals(2, $successfulIrhpCandidatePermits->count());
    }

    public function testGetSuccessfulIrhpCandidatePermitsEuro6EmissionsCategory()
    {
        $irhpCandidatePermits = $this->getPermitsAwardedMocks();
        foreach ($irhpCandidatePermits as $irhpCandidatePermit) {
            $this->sut->addIrhpCandidatePermits($irhpCandidatePermit);
        }

        $successfulIrhpCandidatePermits = $this->sut->getSuccessfulIrhpCandidatePermits(
            RefData::EMISSIONS_CATEGORY_EURO6_REF
        );

        $this->assertTrue($successfulIrhpCandidatePermits->contains($irhpCandidatePermits[4]));
        $this->assertEquals(1, $successfulIrhpCandidatePermits->count());
    }

    private function getPermitsAwardedMocks()
    {
        $euro5EmissionsCategory = new RefData(RefData::EMISSIONS_CATEGORY_EURO5_REF);
        $euro6EmissionsCategory = new RefData(RefData::EMISSIONS_CATEGORY_EURO6_REF);

        $candidatePermit1 = m::mock(IrhpCandidatePermit::class);
        $candidatePermit1->shouldReceive('getSuccessful')
            ->andReturn(true);
        $candidatePermit1->shouldReceive('getAssignedEmissionsCategory')
            ->andReturn($euro5EmissionsCategory);

        $candidatePermit2 = m::mock(IrhpCandidatePermit::class);
        $candidatePermit2->shouldReceive('getSuccessful')
            ->andReturn(false);
        $candidatePermit2->shouldReceive('getAssignedEmissionsCategory')
            ->andReturn($euro5EmissionsCategory);

        $candidatePermit3 = m::mock(IrhpCandidatePermit::class);
        $candidatePermit3->shouldReceive('getSuccessful')
            ->andReturn(true);
        $candidatePermit3->shouldReceive('getAssignedEmissionsCategory')
            ->andReturn($euro5EmissionsCategory);

        $candidatePermit4 = m::mock(IrhpCandidatePermit::class);
        $candidatePermit4->shouldReceive('getSuccessful')
            ->andReturn(false);
        $candidatePermit4->shouldReceive('getAssignedEmissionsCategory')
            ->andReturn($euro6EmissionsCategory);

        $candidatePermit5 = m::mock(IrhpCandidatePermit::class);
        $candidatePermit5->shouldReceive('getSuccessful')
            ->andReturn(true);
        $candidatePermit5->shouldReceive('getAssignedEmissionsCategory')
            ->andReturn($euro6EmissionsCategory);

        return [
            $candidatePermit1,
            $candidatePermit2,
            $candidatePermit3,
            $candidatePermit4,
            $candidatePermit5,
        ];
    }

    public function testHasPermitsRequired()
    {
        $this->assertFalse($this->sut->hasPermitsRequired());

        $this->sut->setPermitsRequired(0);
        $this->assertTrue($this->sut->hasPermitsRequired());
    }

    public function testUpdatePermitsRequired()
    {
        $irhpPermitApplication = Entity::createNew(
            m::mock(IrhpPermitWindow::class),
            m::mock(Licence::class),
            m::mock(EcmtPermitApplication::class)
        );

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('canBeUpdated')
            ->andReturn(true);

        $irhpPermitApplication->setPermitsRequired(44);
        $irhpPermitApplication->setIrhpApplication($irhpApplication);

        $irhpPermitApplication->updatePermitsRequired(4);
        $this->assertEquals(4, $irhpPermitApplication->getPermitsRequired());
    }

    public function testUpdatePermitsRequiredCannotBeUpdated()
    {
        $irhpPermitApplication = Entity::createNew(
            m::mock(IrhpPermitWindow::class),
            m::mock(Licence::class),
            m::mock(EcmtPermitApplication::class)
        );

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('canBeUpdated')
            ->andReturn(false);

        $irhpPermitApplication->setPermitsRequired(44);
        $irhpPermitApplication->setIrhpApplication($irhpApplication);

        $irhpPermitApplication->updatePermitsRequired(4);
        $this->assertEquals(44, $irhpPermitApplication->getPermitsRequired());
    }

    public function testUpdatePermitsRequiredNotApplicable()
    {
        $irhpPermitApplication = Entity::createNew(
            m::mock(IrhpPermitWindow::class),
            m::mock(Licence::class),
            m::mock(EcmtPermitApplication::class)
        );
        $irhpPermitApplication->setPermitsRequired(44);

        $irhpPermitApplication->updatePermitsRequired(4);

        $this->assertEquals(
            44,
            $irhpPermitApplication->getPermitsRequired()
        );
    }

    public function testGetRelatedOrganisationEcmt()
    {
        $org = m::mock(Organisation::class);
        $irhpPermitWindow = m::mock(IrhpPermitWindow::class);
        $licence = m::mock(Licence::class);
        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('getRelatedOrganisation')->once()->withNoArgs()->andReturn($org);

        $entity = Entity::createNew($irhpPermitWindow, $licence, $ecmtPermitApplication);

        $this->assertSame($org, $entity->getRelatedOrganisation());
    }

    public function testGetRelatedOrganisationIrhp()
    {
        $org = m::mock(Organisation::class);
        $irhpPermitWindow = m::mock(IrhpPermitWindow::class);
        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getRelatedOrganisation')->once()->withNoArgs()->andReturn($org);

        $entity = Entity::createNewForIrhpApplication($irhpApplication, $irhpPermitWindow);

        $this->assertSame($org, $entity->getRelatedOrganisation());
    }

    public function testGetRelatedApplicationEcmt()
    {
        $irhpPermitWindow = m::mock(IrhpPermitWindow::class);
        $licence = m::mock(Licence::class);
        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);

        $entity = Entity::createNew($irhpPermitWindow, $licence, $ecmtPermitApplication);

        $this->assertSame($ecmtPermitApplication, $entity->getRelatedApplication());
    }

    public function testGetRelatedApplicationIrhp()
    {
        $irhpPermitWindow = m::mock(IrhpPermitWindow::class);
        $irhpApplication = m::mock(IrhpApplication::class);

        $entity = Entity::createNewForIrhpApplication($irhpApplication, $irhpPermitWindow);

        $this->assertSame($irhpApplication, $entity->getRelatedApplication());
    }

    public function testGetRelatedValuesWhenNothingIsLinked()
    {
        $this->assertNull($this->sut->getRelatedApplication());
        $this->assertNull($this->sut->getRelatedOrganisation());
    }

    public function testGetIssueFeeProductReferenceBilateral()
    {
        $irhpPermitApplication = m::mock(Entity::class)->makePartial();
        $irhpPermitApplication->shouldReceive('getIrhpApplication->getIrhpPermitType->getId')
            ->andReturn(IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL);

        $this->assertEquals(
            FeeType::FEE_TYPE_IRHP_ISSUE_BILATERAL_PRODUCT_REF,
            $irhpPermitApplication->getIssueFeeProductReference()
        );
    }

    /**
     * @dataProvider dpGetIssueFeeProductReferenceMultilateral
     */
    public function testGetIssueFeeProductReferenceMultilateral($dateTimeInput, $expectedFormattedDateTime)
    {
        $tieredProductReference = 'TIERED_PRODUCT_REFERENCE';

        $stockValidFrom = m::mock(DateTime::class);
        $stockValidTo = m::mock(DateTime::class);

        $irhpPermitStock = m::mock(IrhpPermitStock::class);
        $irhpPermitStock->shouldReceive('getValidFrom')
            ->with(true)
            ->andReturn($stockValidFrom);
        $irhpPermitStock->shouldReceive('getValidTo')
            ->with(true)
            ->andReturn($stockValidTo);

        $irhpPermitApplication = m::mock(Entity::class)->makePartial();
        $irhpPermitApplication->shouldReceive('getIrhpApplication->getIrhpPermitType->getId')
            ->andReturn(IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->andReturn($irhpPermitStock);
        $irhpPermitApplication->shouldReceive('genericGetProdRefForTier')
            ->andReturnUsing(
                function (
                    $validityStart,
                    $validityEnd,
                    $now,
                    $tieredProductReferenceArray
                ) use (
                    $tieredProductReference,
                    $stockValidFrom,
                    $stockValidTo,
                    $expectedFormattedDateTime
                ) {
                    $this->assertSame($validityStart, $stockValidFrom);
                    $this->assertSame($validityEnd, $stockValidTo);
                    $this->assertEquals(
                        $now->format('Y-m-d'),
                        $expectedFormattedDateTime
                    );
                    $this->assertEquals(
                        Entity::MULTILATERAL_ISSUE_FEE_PRODUCT_REFERENCE_MONTH_ARRAY,
                        $tieredProductReferenceArray
                    );

                    return $tieredProductReference;
                }
            );

        $this->assertEquals(
            $tieredProductReference,
            $irhpPermitApplication->getIssueFeeProductReference($dateTimeInput)
        );
    }

    public function dpGetIssueFeeProductReferenceMultilateral()
    {
        $dateTime = new DateTime('2029-04-21 13:40:15');

        return [
            [null, (new DateTime())->format('Y-m-d')],
            [$dateTime, $dateTime->format('Y-m-d')]
        ];
    }

    public function testClearPermitsRequired()
    {
        $entity = m::mock(Entity::class)->makePartial();
        $entity->setPermitsRequired(5);
        $entity->clearPermitsRequired();

        $this->assertNull($entity->getPermitsRequired());
    }

    public function testUpdateEmissionsCategoryPermitsRequired()
    {
        $euro5Required = 7;
        $euro6Required = 3;

        $entity = m::mock(Entity::class)->makePartial();
        $entity->updateEmissionsCategoryPermitsRequired($euro5Required, $euro6Required);

        $this->assertEquals($euro5Required, $entity->getRequiredEuro5());
        $this->assertEquals($euro6Required, $entity->getRequiredEuro6());
    }

    public function testClearEmissionsCategoryPermitsRequired()
    {
        $entity = m::mock(Entity::class)->makePartial();
        $entity->updateEmissionsCategoryPermitsRequired(7, 5);
        $entity->clearEmissionsCategoryPermitsRequired();

        $this->assertNull($entity->getRequiredEuro5());
        $this->assertNull($entity->getRequiredEuro6());
    }

    /**
     * @dataProvider dpTestGetTotalEmissionsCategoryPermitsRequired
     */
    public function testGetTotalEmissionsCategoryPermitsRequired($requiredEuro5, $requiredEuro6, $expected)
    {
        $entity = m::mock(Entity::class)->makePartial();
        $entity->setRequiredEuro5($requiredEuro5);
        $entity->setRequiredEuro6($requiredEuro6);

        $this->assertEquals(
            $expected,
            $entity->getTotalEmissionsCategoryPermitsRequired()
        );
    }

    public function dpTestGetTotalEmissionsCategoryPermitsRequired()
    {
        return [
            [null, null, 0],
            [null, 4, 0],
            [4, null, 0],
            [4, 5, 9]
        ];
    }

    public function testUpdateLicence()
    {
        $licence = m::mock(Licence::class);
        $entity = m::mock(Entity::class)->makePartial();

        $entity->updateLicence($licence);

        $this->assertSame($licence, $entity->getLicence());
    }
}
