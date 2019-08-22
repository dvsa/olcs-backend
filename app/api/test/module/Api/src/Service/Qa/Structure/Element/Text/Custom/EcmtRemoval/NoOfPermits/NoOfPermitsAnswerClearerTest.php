<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Text\Custom\EcmtRemoval\NoOfPermits;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom\EcmtRemoval\NoOfPermits\NoOfPermitsAnswerClearer;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * NoOfPermitsAnswerClearerTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsAnswerClearerTest extends MockeryTestCase
{
    private $applicationStep;

    private $irhpPermitApplications;

    private $irhpPermitApplicationRepo;

    private $irhpApplication;

    private $noOfPermitsAnswerClearer;

    public function setUp()
    {
        $this->applicationStep = m::mock(ApplicationStepEntity::class);

        $this->irhpPermitApplications = m::mock(ArrayCollection::class);

        $this->irhpPermitApplicationRepo = m::mock(IrhpPermitApplicationRepository::class);

        $this->irhpApplication = m::mock(IrhpApplicationEntity::class);
        $this->irhpApplication->shouldReceive('getIrhpPermitApplications')
            ->andReturn($this->irhpPermitApplications);

        $this->noOfPermitsAnswerClearer = new NoOfPermitsAnswerClearer($this->irhpPermitApplicationRepo);
    }

    public function testClear()
    {
        $irhpPermitApplication = m::mock(IrhpPermitApplicationEntity::class);

        $this->irhpPermitApplicationRepo->shouldReceive('delete')
            ->with($irhpPermitApplication)
            ->once();

        $this->irhpPermitApplications->shouldReceive('count')
            ->withNoArgs()
            ->andReturn(1);
        $this->irhpPermitApplications->shouldReceive('first')
            ->withNoArgs()
            ->andReturn($irhpPermitApplication);

        $this->noOfPermitsAnswerClearer->clear($this->applicationStep, $this->irhpApplication);
    }

    public function testClearNotRequired()
    {
        $this->irhpPermitApplicationRepo->shouldReceive('delete')
            ->never();

        $this->irhpPermitApplications->shouldReceive('count')
            ->withNoArgs()
            ->andReturn(0);

        $this->noOfPermitsAnswerClearer->clear($this->applicationStep, $this->irhpApplication);
    }
}
