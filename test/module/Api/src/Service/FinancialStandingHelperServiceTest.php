<?php

/**
 * Financial Standing Helper Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Service;


use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\System\FinancialStandingRate;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\FinancialStandingHelperService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Financial Standing Helper Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FinancialStandingHelperServiceTest extends MockeryTestCase
{
    /**
     * @var FinancialStandingHelperService
     */
    protected $sut;

    public function setUp()
    {
        $mockRatesRepo = m::mock();

        $mockRatesRepo
            ->shouldReceive('fetchRatesInEffect')
            ->andReturnUsing([$this, 'getStubRates']);

        // Create service with mocked dependencies
        $this->sut = $this->createService($mockRatesRepo);

        return parent::setUp();
    }

    private function createService($mockRatesRepo)
    {
        $sm = m::mock(ServiceLocatorInterface::class)
            ->shouldReceive('get')
            ->with('RepositoryServiceManager')
            ->andReturn(
                m::mock()
                    ->shouldReceive('get')
                    ->with('FinancialStandingRate')
                    ->andReturn($mockRatesRepo)
                    ->getMock()
            )
            ->getMock();

        $sut = new FinancialStandingHelperService();
        return $sut->createService($sm);
    }

    /**
     * @dataProvider financeCalculationProvider
     * @param array $auths
     * @param int $expected
     */
    public function testGetFinanceCalculation($auths, $expected)
    {
        $this->assertEquals($expected, $this->sut->getFinanceCalculation($auths));
    }

    public function financeCalculationProvider()
    {
        // For an operator:
        //  * with a goods standard international application with 3 vehicles,
        //    the finance is £7000 + (2 x £3900) = £14,800
        //  * plus a goods restricted licence with 3 vehicles, the finance is (3 x £1700) = £5,100
        //  * plus a psv restricted licence with 1 vehicle, the finance is £2,700
        //  * plus another goods app with 2 vehicles (2 x 3900) = £7,800
        //  * The total required finance is £14,800 + £5,100 + £2,700 + £7,800 = £30,400
        return [
            [
                'auths' => array (
                    0 => array (
                      'category' => 'lcat_gv',
                      'type' => 'ltyp_si',
                      'count' => 3,
                    ),
                    1 => array (
                      'category' => 'lcat_gv',
                      'type' => 'ltyp_r',
                      'count' => 3,
                    ),
                    2 => array (
                      'category' => 'lcat_psv',
                      'type' => 'ltyp_r',
                      'count' => 1,
                    ),
                    3 => array (
                      'category' => 'lcat_gv',
                      'type' => 'ltyp_sn',
                      'count' => 2,
                    ),
                ),
                'expected' => 30400,
            ]
        ];
    }

    /**
     * @dataProvider goodsOrPsvProvider
     * @param string $goodsOrPsv
     * @param array $expected
     */
    public function testGetRatesForView($goodsOrPsv, $expected)
    {
        $this->assertEquals($expected, $this->sut->getRatesForView($goodsOrPsv));
    }

    public function goodsOrPsvProvider()
    {
        return [
            [
                'lcat_gv',
                [
                    'standardFirst' => 7000,
                    'standardAdditional' => 3900,
                    'restrictedFirst' => 3100,
                    'restrictedAdditional' => 1700,
                ],
            ],
            [
                'lcat_psv',
                [
                    'standardFirst' => 8000,
                    'standardAdditional' => 4900,
                    'restrictedFirst' => 4100,
                    'restrictedAdditional' => 2700,
                ],
            ],
        ];
    }

    public function testGetAdditionalVehicleRateNull()
    {
        $mockRatesRepo = m::mock()
            ->shouldReceive('fetchRatesInEffect')
            ->andReturn([])
            ->getMock();

        static::assertNull(
            $this->createService($mockRatesRepo)->getAdditionalVehicleRate(null, null)
        );
    }

    public function testGetFirstVehicleRateNull()
    {
        $mockRatesRepo = m::mock()
            ->shouldReceive('fetchRatesInEffect')
            ->andReturn([])
            ->getMock();

        static::assertNull(
            $this->createService($mockRatesRepo)->getFirstVehicleRate(null, null)
        );
    }


    public function getStubRates()
    {
        return [
            $this->getStubRate(7000, 3900, 'lcat_gv', 'ltyp_sn'),
            $this->getStubRate(7000, 3900, 'lcat_gv', 'ltyp_si'),
            $this->getStubRate(3100, 1700, 'lcat_gv', 'ltyp_r'),
            $this->getStubRate(8000, 4900, 'lcat_psv', 'ltyp_sn'),
            $this->getStubRate(8000, 4900, 'lcat_psv', 'ltyp_si'),
            $this->getStubRate(4100, 2700, 'lcat_psv', 'ltyp_r'),
        ];
    }

    protected function getStubRate($firstVehicleRate, $additionalVehicleRate, $goodsOrPsv, $licenceType)
    {
        $rate = new FinancialStandingRate();
        $goodsOrPsvChild = new RefData();
        $goodsOrPsvChild->setId($goodsOrPsv);
        $licenceTypeChild = new RefData();
        $licenceTypeChild->setId($licenceType);

        $rate
            ->setFirstVehicleRate($firstVehicleRate)
            ->setAdditionalVehicleRate($additionalVehicleRate)
            ->setGoodsOrPsv($goodsOrPsvChild)
            ->setLicenceType($licenceTypeChild);

        return $rate;
    }
}
