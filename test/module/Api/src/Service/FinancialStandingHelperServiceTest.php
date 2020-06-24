<?php

/**
 * Financial Standing Helper Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Entity\System\FinancialStandingRate;
use Dvsa\Olcs\Api\Entity;
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

    private $repoMap = [];

    public function setUp(): void
    {
        $this->repoMap['FinancialStandingRate'] = m::mock();
        $this->repoMap['Organisation'] = m::mock();
        $this->repoMap['Application'] = m::mock();

        $this->repoMap['FinancialStandingRate']
            ->shouldReceive('fetchRatesInEffect')
            ->andReturnUsing([$this, 'getStubRates']);

        // Create service with mocked dependencies
        $this->sut = $this->createService();

        parent::setUp();
    }

    private function createService()
    {
        $mockRepoServiceManager = m::mock()
            ->shouldReceive('get')->with('FinancialStandingRate')->once()
                ->andReturn($this->repoMap['FinancialStandingRate'])
            ->shouldReceive('get')->with('Organisation')->once()->andReturn($this->repoMap['Organisation'])
            ->shouldReceive('get')->with('Application')->once()->andReturn($this->repoMap['Application'])
            ->getMock();

        $sm = m::mock(ServiceLocatorInterface::class)
            ->shouldReceive('get')
            ->with('RepositoryServiceManager')
            ->andReturn($mockRepoServiceManager)
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

        $this->assertNull(
            $this->createService($mockRatesRepo)->getAdditionalVehicleRate(null, null)
        );
    }

    public function testGetFirstVehicleRateNull()
    {
        $mockRatesRepo = m::mock()
            ->shouldReceive('fetchRatesInEffect')
            ->andReturn([])
            ->getMock();

        $this->assertNull(
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

    public function testGetFinanceCalculationForOrganisation()
    {
        $organisationId = 69;

        $application1 = m::mock(Entity\Application\Application::class)->makePartial()->setId(1);
        $application1->shouldReceive('getGoodsOrPsv->getId')->andReturn('lcat_gv');
        $application1->shouldReceive('getLicenceType->getId')->andReturn('ltyp_sn');
        $application1->shouldReceive('getTotAuthVehicles')->andReturn(4);

        $application2 = m::mock(Entity\Application\Application::class)->makePartial()->setId(2);
        $application2->shouldReceive('getGoodsOrPsv->getId')->andReturn('lcat_gv');
        $application2->shouldReceive('getLicenceType->getId')->andReturn('ltyp_si');
        $application2->shouldReceive('getTotAuthVehicles')->andReturn(5);

        $application3 = m::mock(Entity\Application\Application::class)->makePartial()->setId(2)->setIsVariation(true);
        $application3->shouldReceive('getGoodsOrPsv->getId')->andReturn('lcat_gv');
        $application3->shouldReceive('getLicenceType->getId')->andReturn('ltyp_si');
        $application3->shouldReceive('getTotAuthVehicles')->andReturn(5);

        $licence1 = m::mock(Entity\Licence\Licence::class)->makePartial()->setId(1);
        $licence1->shouldReceive('getGoodsOrPsv->getId')->andReturn('lcat_psv');
        $licence1->shouldReceive('getLicenceType->getId')->andReturn('ltyp_sn');
        $licence1->shouldReceive('getTotAuthVehicles')->andReturn(6);

        $licence2 = m::mock(Entity\Licence\Licence::class)->makePartial()->setId(2);
        $licence2->shouldReceive('getGoodsOrPsv->getId')->andReturn('lcat_psv');
        $licence2->shouldReceive('getLicenceType->getId')->andReturn('ltyp_r');
        $licence2->shouldReceive('getTotAuthVehicles')->andReturn(7);

        $this->repoMap['Application']
            ->shouldReceive('fetchActiveForOrganisation')
            ->with(69)
            ->andReturn([$application1, $application2, $application3]);

        $organisation = m::mock(Entity\Organisation\Organisation::class)->makePartial()->setId($organisationId);
        $organisation->shouldReceive('getActiveLicences')->with()->once()->andReturn([$licence1, $licence2]);
        $this->repoMap['Organisation']
            ->shouldReceive('fetchById')->with($organisationId)->once()->andReturn($organisation);

        $this->assertEquals(86500, $this->sut->getFinanceCalculationForOrganisation($organisationId));

    }
}
