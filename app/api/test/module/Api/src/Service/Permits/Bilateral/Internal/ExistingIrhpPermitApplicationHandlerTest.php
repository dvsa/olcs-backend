<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepository;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\ApplicationCountryUpdater;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\BilateralRequiredGenerator;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\ExistingIrhpPermitApplicationHandler;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\OtherAnswersUpdater;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\PermitUsageSelectionGenerator;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\ApplicationAnswersClearer;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\NoOfPermitsUpdater;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * ExistingIrhpPermitApplicationHandlerTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ExistingIrhpPermitApplicationHandlerTest extends MockeryTestCase
{
    const EXISTING_STOCK_ID = 10;

    const EXISTING_PERMIT_USAGE_SELECTION = RefData::JOURNEY_SINGLE;

    const EXISTING_BILATERAL_REQUIRED = [
        IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 5,
        IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 7
    ];

    const CHANGED_STOCK_ID = 11;

    const CHANGED_PERMIT_USAGE_SELECTION = RefData::JOURNEY_MULTIPLE;

    const CHANGED_BILATERAL_REQUIRED = [
        IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 9,
        IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 4
    ];

    const REQUIRED_PERMITS = [
        'requiredPermitsKey1' => 'requiredPermitsValue1',
        'requiredPermitsKey2' => 'requiredPermitsValue2'
    ];

    private $irhpPermitApplication;

    private $irhpPermitApplicationRepo;

    private $irhpPermitStockRepo;

    private $permitUsageSelectionGenerator;

    private $bilateralRequiredGenerator;

    private $otherAnswersUpdater;

    private $noOfPermitsUpdater;

    private $applicationAnswersClearer;

    private $existingIrhpPermitApplicationHandler;

    public function setUp(): void
    {
        $this->irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $this->irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getId')
            ->withNoArgs()
            ->andReturn(self::EXISTING_STOCK_ID);
        $this->irhpPermitApplication->shouldReceive('getBilateralPermitUsageSelection')
            ->withNoArgs()
            ->andReturn(self::EXISTING_PERMIT_USAGE_SELECTION);
        $this->irhpPermitApplication->shouldReceive('getBilateralRequired')
            ->withNoArgs()
            ->andReturn(self::EXISTING_BILATERAL_REQUIRED);

        $this->irhpPermitApplicationRepo = m::mock(IrhpPermitApplicationRepository::class);

        $this->irhpPermitStockRepo = m::mock(IrhpPermitStockRepository::class);

        $this->permitUsageSelectionGenerator = m::mock(PermitUsageSelectionGenerator::class);

        $this->bilateralRequiredGenerator = m::mock(BilateralRequiredGenerator::class);

        $this->otherAnswersUpdater = m::mock(OtherAnswersUpdater::class);

        $this->noOfPermitsUpdater = m::mock(NoOfPermitsUpdater::class);

        $this->applicationAnswersClearer = m::mock(ApplicationAnswersClearer::class);

        $this->existingIrhpPermitApplicationHandler = new ExistingIrhpPermitApplicationHandler(
            $this->irhpPermitApplicationRepo,
            $this->irhpPermitStockRepo,
            $this->permitUsageSelectionGenerator,
            $this->bilateralRequiredGenerator,
            $this->otherAnswersUpdater,
            $this->noOfPermitsUpdater,
            $this->applicationAnswersClearer
        );
    }

    /**
     * @dataProvider dpGenerate
     */
    public function testHandle($newStockId, $newPermitUsageSelection, $newBilateralRequired)
    {
        $irhpPermitWindow = m::mock(IrhpPermitWindow::class);

        $irhpPermitStock = m::mock(IrhpPermitStock::class);
        $irhpPermitStock->shouldReceive('getOpenWindow')
            ->withNoArgs()
            ->andReturn($irhpPermitWindow);

        $this->irhpPermitStockRepo->shouldReceive('fetchById')
            ->with($newStockId)
            ->andReturn($irhpPermitStock);

        $this->permitUsageSelectionGenerator->shouldReceive('generate')
            ->with(self::REQUIRED_PERMITS)
            ->andReturn($newPermitUsageSelection);

        $this->bilateralRequiredGenerator->shouldReceive('generate')
            ->with(self::REQUIRED_PERMITS, $newPermitUsageSelection)
            ->andReturn($newBilateralRequired);

        $this->applicationAnswersClearer->shouldReceive('clear')
            ->with($this->irhpPermitApplication)
            ->once()
            ->globally()
            ->ordered();

        $this->irhpPermitApplication->shouldReceive('updateIrhpPermitWindow')
            ->with($irhpPermitWindow)
            ->once()
            ->globally()
            ->ordered();

        $this->otherAnswersUpdater->shouldReceive('update')
            ->with($this->irhpPermitApplication, $newBilateralRequired, $newPermitUsageSelection)
            ->once()
            ->globally()
            ->ordered();

        $this->noOfPermitsUpdater->shouldReceive('update')
            ->with($this->irhpPermitApplication, $newBilateralRequired)
            ->once()
            ->globally()
            ->ordered();

        $this->existingIrhpPermitApplicationHandler->handle(
            $this->irhpPermitApplication,
            $newStockId,
            self::REQUIRED_PERMITS
        );
    }

    public function dpGenerate()
    {
        return [
            [self::EXISTING_STOCK_ID, self::EXISTING_PERMIT_USAGE_SELECTION, self::CHANGED_BILATERAL_REQUIRED],
            [self::EXISTING_STOCK_ID, self::CHANGED_PERMIT_USAGE_SELECTION, self::EXISTING_BILATERAL_REQUIRED],
            [self::EXISTING_STOCK_ID, self::CHANGED_PERMIT_USAGE_SELECTION, self::CHANGED_BILATERAL_REQUIRED],
            [self::CHANGED_STOCK_ID, self::EXISTING_PERMIT_USAGE_SELECTION, self::EXISTING_BILATERAL_REQUIRED],
            [self::CHANGED_STOCK_ID, self::EXISTING_PERMIT_USAGE_SELECTION, self::CHANGED_BILATERAL_REQUIRED],
            [self::CHANGED_STOCK_ID, self::CHANGED_PERMIT_USAGE_SELECTION, self::EXISTING_BILATERAL_REQUIRED],
            [self::CHANGED_STOCK_ID, self::CHANGED_PERMIT_USAGE_SELECTION, self::CHANGED_BILATERAL_REQUIRED],
        ];
    }

    public function testHandleNoChange()
    {
        $this->permitUsageSelectionGenerator->shouldReceive('generate')
            ->with(self::REQUIRED_PERMITS)
            ->andReturn(self::EXISTING_PERMIT_USAGE_SELECTION);

        $this->bilateralRequiredGenerator->shouldReceive('generate')
            ->with(self::REQUIRED_PERMITS, self::EXISTING_PERMIT_USAGE_SELECTION)
            ->andReturn(self::EXISTING_BILATERAL_REQUIRED);

        $this->irhpPermitApplication->shouldReceive('updateCheckAnswers')
            ->withNoArgs()
            ->once()
            ->globally()
            ->ordered();

        $this->irhpPermitApplicationRepo->shouldReceive('save')
            ->with($this->irhpPermitApplication)
            ->once()
            ->globally()
            ->ordered();

        $this->existingIrhpPermitApplicationHandler->handle(
            $this->irhpPermitApplication,
            self::EXISTING_STOCK_ID,
            self::REQUIRED_PERMITS
        );
    }
}
