<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Doctrine\Common\Collections\ArrayCollection;
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
        $this->sut = new Entity(m::mock(\Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication::class));

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

    public function testGetCalculatedBundleValues()
    {
        $this->assertSame(
            [
                'permitsAwarded' => 0,
                'validPermits' => 0
            ],
            $this->sut->getCalculatedBundleValues()
        );
    }

    public function testCountValidPermits()
    {
        $candidatePermit1 = m::mock(IrhpCandidatePermit::class);
        $candidatePermit1->shouldReceive('getIrhpPermits')
            ->andReturn(null);
        $candidatePermit1->shouldReceive('getSuccessful')
            ->andReturn(true);

        $candidatePermit2Permits = new ArrayCollection([
            m::mock(IrhpPermit::class),
        ]);
        $candidatePermit2 = m::mock(IrhpCandidatePermit::class);
        $candidatePermit2->shouldReceive('getIrhpPermits')
            ->andReturn($candidatePermit2Permits);
        $candidatePermit2->shouldReceive('getSuccessful')
            ->andReturn(true);

        $candidatePermit3Permits = new ArrayCollection([
            m::mock(IrhpPermit::class),
        ]);
        $candidatePermit3 = m::mock(IrhpCandidatePermit::class);
        $candidatePermit3->shouldReceive('getIrhpPermits')
            ->andReturn($candidatePermit3Permits);
        $candidatePermit3->shouldReceive('getSuccessful')
            ->andReturn(false);

        $candidatePermit4Permits = new ArrayCollection([
            m::mock(IrhpPermit::class),
        ]);
        $candidatePermit4 = m::mock(IrhpCandidatePermit::class);
        $candidatePermit4->shouldReceive('getIrhpPermits')
            ->andReturn($candidatePermit4Permits);
        $candidatePermit4->shouldReceive('getSuccessful')
            ->andReturn(true);

        $this->sut->addIrhpCandidatePermits($candidatePermit1);
        $this->sut->addIrhpCandidatePermits($candidatePermit2);
        $this->sut->addIrhpCandidatePermits($candidatePermit3);
        $this->sut->addIrhpCandidatePermits($candidatePermit4);

        $this->assertEquals(2, $this->sut->countValidPermits());
    }

    public function testCountPermitsAwarded()
    {
        $candidatePermit1 = m::mock(IrhpCandidatePermit::class);
        $candidatePermit1->shouldReceive('getSuccessful')
            ->andReturn(true);

        $candidatePermit2 = m::mock(IrhpCandidatePermit::class);
        $candidatePermit2->shouldReceive('getSuccessful')
            ->andReturn(false);

        $candidatePermit3 = m::mock(IrhpCandidatePermit::class);
        $candidatePermit3->shouldReceive('getSuccessful')
            ->andReturn(true);

        $this->sut->addIrhpCandidatePermits($candidatePermit1);
        $this->sut->addIrhpCandidatePermits($candidatePermit2);
        $this->sut->addIrhpCandidatePermits($candidatePermit3);

        $this->assertEquals(2, $this->sut->countPermitsAwarded());
    }

    public function testGetSuccessfulIrhpCandidatePermits()
    {
        $candidatePermit1 = m::mock(IrhpCandidatePermit::class);
        $candidatePermit1->shouldReceive('getSuccessful')
            ->andReturn(true);

        $candidatePermit2 = m::mock(IrhpCandidatePermit::class);
        $candidatePermit2->shouldReceive('getSuccessful')
            ->andReturn(false);

        $candidatePermit3 = m::mock(IrhpCandidatePermit::class);
        $candidatePermit3->shouldReceive('getSuccessful')
            ->andReturn(true);

        $this->sut->addIrhpCandidatePermits($candidatePermit1);
        $this->sut->addIrhpCandidatePermits($candidatePermit2);
        $this->sut->addIrhpCandidatePermits($candidatePermit3);

        $successfulIrhpCandidatePermits = $this->sut->getSuccessfulIrhpCandidatePermits();

        $this->assertTrue($successfulIrhpCandidatePermits->contains($candidatePermit1));
        $this->assertTrue($successfulIrhpCandidatePermits->contains($candidatePermit3));
        $this->assertEquals(2, $successfulIrhpCandidatePermits->count());
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
}
