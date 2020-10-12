<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\NoOfPermitsMoroccoAnswerSaver;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\NoOfPermitsUpdater;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * NoOfPermitsMoroccoAnswerSaverTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsMoroccoAnswerSaverTest extends MockeryTestCase
{
    const POST_DATA = [
        'key1' => 'value1',
        'key2' => 'value2',
    ];

    private $irhpPermitApplication;

    private $applicationStep;

    private $qaContext;

    private $genericAnswerFetcher;

    private $noOfPermitsUpdater;

    private $noOfPermitsMoroccoAnswerSaver;

    public function setUp(): void
    {
        $defaultBilateralRequired = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => null,
            IrhpPermitApplication::BILATERAL_MOROCCO_REQUIRED => null,
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

        $this->genericAnswerFetcher = m::mock(GenericAnswerFetcher::class);

        $this->noOfPermitsUpdater = m::mock(NoOfPermitsUpdater::class);

        $this->noOfPermitsMoroccoAnswerSaver = new NoOfPermitsMoroccoAnswerSaver(
            $this->genericAnswerFetcher,
            $this->noOfPermitsUpdater
        );
    }

    public function testSaveUpdateRequired()
    {
        $oldPermitsRequired = 12;
        $newPermitsRequired = 14;

        $bilateralRequired = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => null,
            IrhpPermitApplication::BILATERAL_MOROCCO_REQUIRED => $oldPermitsRequired,
        ];

        $this->irhpPermitApplication->shouldReceive('getBilateralRequired')
            ->withNoArgs()
            ->andReturn($bilateralRequired);

        $this->genericAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, self::POST_DATA)
            ->andReturn($newPermitsRequired);

        $expectedUpdatedAnswers = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => null,
            IrhpPermitApplication::BILATERAL_MOROCCO_REQUIRED => $newPermitsRequired,
        ];

        $this->noOfPermitsUpdater->shouldReceive('update')
            ->with($this->irhpPermitApplication, $expectedUpdatedAnswers)
            ->once();

        $this->noOfPermitsMoroccoAnswerSaver->save($this->qaContext, self::POST_DATA);
    }

    public function testSaveUpdateNotRequired()
    {
        $oldPermitsRequired = 13;
        $newPermitsRequired = 13;

        $bilateralRequired = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => null,
            IrhpPermitApplication::BILATERAL_MOROCCO_REQUIRED => $oldPermitsRequired,
        ];

        $this->irhpPermitApplication->shouldReceive('getBilateralRequired')
            ->withNoArgs()
            ->andReturn($bilateralRequired);

        $this->genericAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, self::POST_DATA)
            ->andReturn($newPermitsRequired);

        $this->noOfPermitsUpdater->shouldReceive('update')
            ->never();

        $this->noOfPermitsMoroccoAnswerSaver->save($this->qaContext, self::POST_DATA);
    }
}
