<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\ApplicationCountryUpdater;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\ExistingIrhpPermitApplicationHandler;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\IrhpPermitApplicationCreator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * ApplicationCountryUpdaterTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ApplicationCountryUpdaterTest extends MockeryTestCase
{
    const COUNTRY_ID = 'DE';

    const STOCK_ID = 44;

    const REQUIRED_PERMITS = [
        'key1' => 'value1',
        'key2' => 'value2',
    ];

    private $irhpApplication;

    private $irhpPermitApplicationCreator;

    private $existingIrhpPermitApplicationHandler;

    private $applicationCountryUpdater;

    public function setUp()
    {
        $this->irhpApplication = m::mock(IrhpApplication::class);

        $this->irhpPermitApplicationCreator = m::mock(IrhpPermitApplicationCreator::class);

        $this->existingIrhpPermitApplicationHandler = m::mock(ExistingIrhpPermitApplicationHandler::class);

        $this->applicationCountryUpdater = new ApplicationCountryUpdater(
            $this->irhpPermitApplicationCreator,
            $this->existingIrhpPermitApplicationHandler
        );
    }

    public function testUpdateIrhpPermitApplicationExists()
    {
        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);

        $this->irhpApplication->shouldReceive('getIrhpPermitApplicationByStockCountryId')
            ->with(self::COUNTRY_ID)
            ->andReturn($irhpPermitApplication);

        $this->existingIrhpPermitApplicationHandler->shouldReceive('handle')
            ->with($irhpPermitApplication, self::STOCK_ID, self::REQUIRED_PERMITS)
            ->once();

        $this->applicationCountryUpdater->update(
            $this->irhpApplication,
            self::COUNTRY_ID,
            self::STOCK_ID,
            self::REQUIRED_PERMITS
        );
    }

    public function testUpdateIrhpPermitApplicationNotFound()
    {
        $this->irhpApplication->shouldReceive('getIrhpPermitApplicationByStockCountryId')
            ->with(self::COUNTRY_ID)
            ->andReturnNull();

        $this->irhpPermitApplicationCreator->shouldReceive('create')
            ->with($this->irhpApplication, self::STOCK_ID, self::REQUIRED_PERMITS)
            ->once();

        $this->applicationCountryUpdater->update(
            $this->irhpApplication,
            self::COUNTRY_ID,
            self::STOCK_ID,
            self::REQUIRED_PERMITS
        );
    }
}
