<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Common;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Service\Permits\Common\TypeBasedRestrictedCountriesProvider;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

/**
 * TypeBasedRestrictedCountriessProviderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class TypeBasedRestrictedCountriesProviderTest extends MockeryTestCase
{
    private $ecmtAnnualRestrictedCountries = ['FR', 'DE'];

    private $ecmtShortTermRestrictedCountries = ['HU' ,'RU', 'IT'];

    private $config;

    private $typeBasedRestrictedCountriesProvider;

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

        $this->typeBasedRestrictedCountriesProvider = new TypeBasedRestrictedCountriesProvider(
            $this->config
        );

        parent::setUp();
    }

    /**
     * @dataProvider dpTestGetIds
     */
    public function testGetIds($irhpPermitTypeId, $expectedRestrictedCountries)
    {
        $this->assertEquals(
            $expectedRestrictedCountries,
            $this->typeBasedRestrictedCountriesProvider->getIds($irhpPermitTypeId)
        );
    }

    public function dpTestGetIds()
    {
        return [
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT, $this->ecmtAnnualRestrictedCountries],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, $this->ecmtShortTermRestrictedCountries],
        ];
    }

    public function testGetIdsMissingConfig()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No restricted countries config found for permit type 99');

        $this->typeBasedRestrictedCountriesProvider->getIds(99);
    }
}
