<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Common;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepository;
use Dvsa\Olcs\Api\Service\Permits\Common\StockBasedRestrictedCountryIdsProvider;
use Dvsa\Olcs\Api\Service\Permits\Common\TypeBasedRestrictedCountriesProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * StockBasedRestrictedCountryIdsProviderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class StockBasedRestrictedCountryIdsProviderTest extends MockeryTestCase
{
    private $restrictedCountryIds = ['HU' ,'RU', 'IT'];

    private $irhpPermitStockId = 42;

    private $irhpPermitStock;

    private $irhpPermitStockRepo;

    private $stockBasedRestrictedCountryIdsProvider;

    private $typeBasedRestrictedCountriesProvider;

    public function setUp()
    {
        $this->irhpPermitStock = m::mock(IrhpPermitStock::class);

        $this->irhpPermitStockRepo = m::mock(IrhpPermitStockRepository::class);
        $this->irhpPermitStockRepo->shouldReceive('fetchById')
            ->with($this->irhpPermitStockId)
            ->once()
            ->andReturn($this->irhpPermitStock);

        $this->typeBasedRestrictedCountriesProvider = m::mock(TypeBasedRestrictedCountriesProvider::class);

        $this->stockBasedRestrictedCountryIdsProvider = new StockBasedRestrictedCountryIdsProvider(
            $this->irhpPermitStockRepo,
            $this->typeBasedRestrictedCountriesProvider
        );

        parent::setUp();
    }

    public function testGetIds()
    {
        $irhpPermitTypeId = IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM;

        $this->irhpPermitStock->shouldReceive('getIrhpPermitType->getId')
            ->withNoArgs()
            ->andReturn($irhpPermitTypeId);

        $this->typeBasedRestrictedCountriesProvider->shouldReceive('getIds')
            ->with($irhpPermitTypeId)
            ->once()
            ->andReturn($this->restrictedCountryIds);

        $this->assertEquals(
            $this->restrictedCountryIds,
            $this->stockBasedRestrictedCountryIdsProvider->getIds($this->irhpPermitStockId)
        );
    }
}
