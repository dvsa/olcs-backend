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
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\IrhpPermitApplicationCreator;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\IrhpPermitApplicationFactory;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\OtherAnswersUpdater;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\PermitUsageSelectionGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\NoOfPermitsUpdater;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * IrhpPermitApplicationCreatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IrhpPermitApplicationCreatorTest extends MockeryTestCase
{
    public function testHandle()
    {
        $bilateralRequiredGenerator = m::mock(BilateralRequiredGenerator::class);

        $requiredPermits = [
            'requiredPermitsKey1' => 'requiredPermitsValue1',
            'requiredPermitsKey2' => 'requiredPermitsValue2'
        ];

        $stockId = 47;
        
        $permitUsageSelection = RefData::JOURNEY_MULTIPLE;

        $bilateralRequired = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 7,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 10
        ];

        $irhpPermitWindow = m::mock(IrhpPermitWindow::class);

        $irhpPermitStock = m::mock(IrhpPermitStock::class);
        $irhpPermitStock->shouldReceive('getOpenWindow')
            ->withNoArgs()
            ->andReturn($irhpPermitWindow);

        $irhpPermitStockRepo = m::mock(IrhpPermitStockRepository::class);
        $irhpPermitStockRepo->shouldReceive('fetchById')
            ->with($stockId)
            ->andReturn($irhpPermitStock);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getId')
            ->withNoArgs()
            ->andReturn($stockId);
        $irhpPermitApplication->shouldReceive('getBilateralPermitUsageSelection')
            ->withNoArgs()
            ->andReturn($permitUsageSelection);
        $irhpPermitApplication->shouldReceive('getBilateralRequired')
            ->withNoArgs()
            ->andReturn($bilateralRequired);

        $irhpApplication = m::mock(IrhpApplication::class);

        $irhpPermitApplicationFactory = m::mock(IrhpPermitApplicationFactory::class);
        $irhpPermitApplicationFactory->shouldReceive('create')
            ->with($irhpApplication, $irhpPermitWindow)
            ->andReturn($irhpPermitApplication);

        $permitUsageSelectionGenerator = m::mock(PermitUsageSelectionGenerator::class);
        $permitUsageSelectionGenerator->shouldReceive('generate')
            ->with($requiredPermits)
            ->andReturn($permitUsageSelection);

        $bilateralRequiredGenerator->shouldReceive('generate')
            ->with($requiredPermits, $permitUsageSelection)
            ->andReturn($bilateralRequired);

        $irhpPermitApplicationRepo = m::mock(IrhpPermitApplicationRepository::class);
        $irhpPermitApplicationRepo->shouldReceive('save')
            ->with($irhpPermitApplication)
            ->once()
            ->globally()
            ->ordered();

        $otherAnswersUpdater = m::mock(OtherAnswersUpdater::class);
        $otherAnswersUpdater->shouldReceive('update')
            ->with($irhpPermitApplication, $bilateralRequired, $permitUsageSelection)
            ->once()
            ->globally()
            ->ordered();

        $noOfPermitsUpdater = m::mock(NoOfPermitsUpdater::class);
        $noOfPermitsUpdater->shouldReceive('update')
            ->with($irhpPermitApplication, $bilateralRequired)
            ->once()
            ->globally()
            ->ordered();

        $irhpPermitApplicationCreator = new IrhpPermitApplicationCreator(
            $irhpPermitStockRepo,
            $irhpPermitApplicationRepo,
            $irhpPermitApplicationFactory,
            $permitUsageSelectionGenerator,
            $bilateralRequiredGenerator,
            $otherAnswersUpdater,
            $noOfPermitsUpdater
        );

        $irhpPermitApplicationCreator->create(
            $irhpApplication,
            $stockId,
            $requiredPermits
        );
    }
}
