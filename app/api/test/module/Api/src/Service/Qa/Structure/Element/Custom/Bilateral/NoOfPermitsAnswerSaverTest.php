<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\NoOfPermitsAnswerSaver;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\NoOfPermitsUpdater;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\NamedAnswerFetcher;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * NoOfPermitsAnswerSaverTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsAnswerSaverTest extends MockeryTestCase
{
    private $postData = [
        'key1' => 'value1',
        'key2' => 'value2',
    ];

    private $irhpPermitApplication;

    private $applicationStep;

    private $qaContext;

    private $namedAnswerFetcher;

    private $noOfPermitsUpdater;

    private $noOfPermitsAnswerSaver;

    public function setUp()
    {
        $defaultBilateralRequired = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => null,
        ];

        $this->irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $this->irhpPermitApplication->shouldReceive('getDefaultBilateralRequired')
            ->withNoArgs()
            ->andReturn($defaultBilateralRequired);

        $this->applicationStep = m::mock(ApplicationStep::class);

        $this->qaContext = m::mock(QaContext::class);
        $this->qaContext->shouldReceive('getQaEntity')
            ->withNoArgs()
            ->andReturn($this->irhpPermitApplication);
        $this->qaContext->shouldReceive('getApplicationStepEntity')
            ->withNoArgs()
            ->andReturn($this->applicationStep);

        $this->namedAnswerFetcher = m::mock(NamedAnswerFetcher::class);

        $this->noOfPermitsUpdater = m::mock(NoOfPermitsUpdater::class);

        $this->noOfPermitsAnswerSaver = new NoOfPermitsAnswerSaver(
            $this->namedAnswerFetcher,
            $this->noOfPermitsUpdater
        );
    }

    public function testSaveStandardOnlyUpdateRequired()
    {
        $oldStandardPermitsRequired = 12;
        $newStandardPermitsRequired = 14;

        $bilateralRequired = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => $oldStandardPermitsRequired,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => null,
        ];

        $this->irhpPermitApplication->shouldReceive('getBilateralRequired')
            ->withNoArgs()
            ->andReturn($bilateralRequired);
        $this->irhpPermitApplication->shouldReceive('getBilateralCabotageSelection')
            ->withNoArgs()
            ->andReturn(Answer::BILATERAL_STANDARD_ONLY);

        $this->namedAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, $this->postData, IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED)
            ->andReturn($newStandardPermitsRequired);

        $expectedUpdatedAnswers = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => $newStandardPermitsRequired,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => null,
        ];

        $this->noOfPermitsUpdater->shouldReceive('update')
            ->with($this->irhpPermitApplication, $expectedUpdatedAnswers)
            ->once();

        $this->noOfPermitsAnswerSaver->save($this->qaContext, $this->postData);
    }

    public function testSaveStandardOnlyUpdateNotRequired()
    {
        $oldStandardPermitsRequired = 13;
        $newStandardPermitsRequired = 13;

        $bilateralRequired = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => $oldStandardPermitsRequired,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => null,
        ];

        $this->irhpPermitApplication->shouldReceive('getBilateralRequired')
            ->withNoArgs()
            ->andReturn($bilateralRequired);
        $this->irhpPermitApplication->shouldReceive('getBilateralCabotageSelection')
            ->withNoArgs()
            ->andReturn(Answer::BILATERAL_STANDARD_ONLY);

        $this->namedAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, $this->postData, IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED)
            ->andReturn($newStandardPermitsRequired);

        $this->noOfPermitsUpdater->shouldReceive('update')
            ->never();

        $this->noOfPermitsAnswerSaver->save($this->qaContext, $this->postData);
    }

    public function testSaveCabotageOnlyUpdateRequired()
    {
        $oldCabotagePermitsRequired = 12;
        $newCabotagePermitsRequired = 14;

        $bilateralRequired = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => $oldCabotagePermitsRequired,
        ];

        $this->irhpPermitApplication->shouldReceive('getBilateralRequired')
            ->withNoArgs()
            ->andReturn($bilateralRequired);
        $this->irhpPermitApplication->shouldReceive('getBilateralCabotageSelection')
            ->withNoArgs()
            ->andReturn(Answer::BILATERAL_CABOTAGE_ONLY);

        $this->namedAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, $this->postData, IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED)
            ->andReturn($newCabotagePermitsRequired);

        $expectedUpdatedAnswers = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => $newCabotagePermitsRequired,
        ];

        $this->noOfPermitsUpdater->shouldReceive('update')
            ->with($this->irhpPermitApplication, $expectedUpdatedAnswers)
            ->once();

        $this->noOfPermitsAnswerSaver->save($this->qaContext, $this->postData);
    }

    public function testSaveCabotageOnlyUpdateNotRequired()
    {
        $oldCabotagePermitsRequired = 15;
        $newCabotagePermitsRequired = 15;

        $bilateralRequired = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => $oldCabotagePermitsRequired,
        ];

        $this->irhpPermitApplication->shouldReceive('getBilateralRequired')
            ->withNoArgs()
            ->andReturn($bilateralRequired);
        $this->irhpPermitApplication->shouldReceive('getBilateralCabotageSelection')
            ->withNoArgs()
            ->andReturn(Answer::BILATERAL_CABOTAGE_ONLY);

        $this->namedAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, $this->postData, IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED)
            ->andReturn($newCabotagePermitsRequired);

        $this->noOfPermitsUpdater->shouldReceive('update')
            ->never();

        $this->noOfPermitsAnswerSaver->save($this->qaContext, $this->postData);
    }

    /**
     * @dataProvider dpSaveStandardAndCabotageUpdateRequired
     */
    public function testSaveStandardAndCabotageUpdateRequired($newStandardPermitsRequired, $newCabotagePermitsRequired)
    {
        $oldStandardPermitsRequired = 11;
        $oldCabotagePermitsRequired = 12;

        $bilateralRequired = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => $oldStandardPermitsRequired,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => $oldCabotagePermitsRequired,
        ];

        $this->irhpPermitApplication->shouldReceive('getBilateralRequired')
            ->withNoArgs()
            ->andReturn($bilateralRequired);
        $this->irhpPermitApplication->shouldReceive('getBilateralCabotageSelection')
            ->withNoArgs()
            ->andReturn(Answer::BILATERAL_STANDARD_AND_CABOTAGE);

        $this->namedAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, $this->postData, IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED)
            ->andReturn($newStandardPermitsRequired);
        $this->namedAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, $this->postData, IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED)
            ->andReturn($newCabotagePermitsRequired);

        $expectedUpdatedAnswers = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => $newStandardPermitsRequired,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => $newCabotagePermitsRequired,
        ];

        $this->noOfPermitsUpdater->shouldReceive('update')
            ->with($this->irhpPermitApplication, $expectedUpdatedAnswers)
            ->once();

        $this->noOfPermitsAnswerSaver->save($this->qaContext, $this->postData);
    }

    public function dpSaveStandardAndCabotageUpdateRequired()
    {
        return [
            [14, 15],
            [20, 12],
            [11, 17],
        ];
    }

    public function testSaveStandardAndCabotageUpdateNotRequired()
    {
        $oldStandardPermitsRequired = 16;
        $oldCabotagePermitsRequired = 10;

        $newStandardPermitsRequired = 16;
        $newCabotagePermitsRequired = 10;

        $bilateralRequired = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => $oldStandardPermitsRequired,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => $oldCabotagePermitsRequired,
        ];

        $this->irhpPermitApplication->shouldReceive('getBilateralRequired')
            ->withNoArgs()
            ->andReturn($bilateralRequired);
        $this->irhpPermitApplication->shouldReceive('getBilateralCabotageSelection')
            ->withNoArgs()
            ->andReturn(Answer::BILATERAL_STANDARD_AND_CABOTAGE);

        $this->namedAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, $this->postData, IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED)
            ->andReturn($newStandardPermitsRequired);
        $this->namedAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, $this->postData, IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED)
            ->andReturn($newCabotagePermitsRequired);

        $this->noOfPermitsUpdater->shouldReceive('update')
            ->never();

        $this->noOfPermitsAnswerSaver->save($this->qaContext, $this->postData);
    }
}
