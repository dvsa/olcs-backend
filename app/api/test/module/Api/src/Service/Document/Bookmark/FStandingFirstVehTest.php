<?php
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\FStandingFirstVeh;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * FStandingFirstVeh bookmark test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FStandingFirstVehTest extends MockeryTestCase
{
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new FStandingFirstVeh();
    }

    public function testGetQuery()
    {
        $mockDateHelper = m::mock('Dvsa\Olcs\Api\Service\Date')
            ->shouldReceive('getDate')
            ->with('Y-m-d')
            ->andReturn('2015-05-01')
            ->once()
            ->getMock();
        $this->sut->setDateHelper($mockDateHelper);

        $data = [
            'goodsOrPsv' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
            'licenceType' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
        ];
        $query = $this->sut->getQuery($data);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRenderWithNoFirstVehicleFee()
    {
        $this->sut->setData([]);
        $this->assertEquals('', $this->sut->render());
    }

    public function testRenderWithFirstVehicleFee()
    {
        $this->sut->setData(
            [
                'Count' => 2,
                'Results' => [
                    [
                        'firstVehicleRate' => '123456',
                        'effectiveFrom' => '2015-01-01'
                    ],
                    [
                        'firstVehicleRate' => '023444',
                        'effectiveFrom' => '2013-01-01'
                    ]
                ]
            ]
        );

        $this->assertEquals(
            '123,456',
            $this->sut->render()
        );
    }
}
