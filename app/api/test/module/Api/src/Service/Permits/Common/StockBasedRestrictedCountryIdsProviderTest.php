<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Common;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepository;
use Dvsa\Olcs\Api\Service\Permits\Common\StockBasedRestrictedCountryIdsProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

/**
 * StockBasedRestrictedCountryIdsProviderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class StockBasedRestrictedCountryIdsProviderTest extends MockeryTestCase
{
    private $ecmtAnnualRestrictedCountries = ['FR', 'DE'];

    private $ecmtShortTermRestrictedCountries = ['HU' ,'RU', 'IT'];

    private $config;

    private $irhpPermitStockId = 42;

    private $irhpPermitStock;

    private $irhpPermitStockRepo;

    private $stockBasedRestrictedCountryIdsProvider;

    public function setUp()
    {
        $this->config = [
            'permits' => [
                'types' => [
                    IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT => [
                        'restricted_countries' => $this->ecmtAnnualRestrictedCountries,
                    ],
                    IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM => [
                        'restricted_countries' => $this->ecmtShortTermRestrictedCountries,
                    ],
                ]
            ]
        ];

        $this->irhpPermitStock = m::mock(IrhpPermitStock::class);

        $this->irhpPermitStockRepo = m::mock(IrhpPermitStockRepository::class);
        $this->irhpPermitStockRepo->shouldReceive('fetchById')
            ->with($this->irhpPermitStockId)
            ->once()
            ->andReturn($this->irhpPermitStock);

        $this->forCpProviderFactory = m::mock(ForCpProviderFactory::class);

        $this->stockBasedRestrictedCountryIdsProvider = new StockBasedRestrictedCountryIdsProvider(
            $this->irhpPermitStockRepo,
            $this->config
        );
    }

    /**
     * @dataProvider dpTestCreate
     */
    public function testCreate($irhpPermitTypeId, $expectedRestrictedCountries)
    {
        $this->irhpPermitStock->shouldReceive('getIrhpPermitType->getId')
            ->withNoArgs()
            ->andReturn($irhpPermitTypeId);

        $this->assertEquals(
            $expectedRestrictedCountries,
            $this->stockBasedRestrictedCountryIdsProvider->getIds($this->irhpPermitStockId)
        );
    }

    public function dpTestCreate()
    {
        return [
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT, $this->ecmtAnnualRestrictedCountries],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, $this->ecmtShortTermRestrictedCountries],
        ];
    }

    public function testCreateMissingConfig()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No restricted countries config found for permit type 99');

        $this->irhpPermitStock->shouldReceive('getIrhpPermitType->getId')
            ->withNoArgs()
            ->andReturn(99);

        $this->stockBasedRestrictedCountryIdsProvider->getIds($this->irhpPermitStockId);
    }
}
