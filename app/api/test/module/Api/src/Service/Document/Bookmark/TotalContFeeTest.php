<?php
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\TotalContFee;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;

/**
 * TotalContFeeTest bookmark test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TotalContFeeTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new TotalContFee();
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
            'niFlag' => 'N',
            'trafficAreaId' => 'X'
        ];
        $query = $this->sut->getQuery($data);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRenderWithNoTotalContFee()
    {
        $this->sut->setData(null);
        $this->assertEquals('', $this->sut->render());
    }

    /**
     * @dataProvider resultsProvider
     */
    public function testRenderWithTotalContFee($results)
    {
        $this->sut->setData($results);
        $this->assertEquals(
            '123,456',
            $this->sut->render()
        );
    }

    public function resultsProvider()
    {
        return [
            [
                [
                    'fixedValue' => '123456',
                    'effectiveFrom' => '2015-01-01'
                ],
            ],
            [
                [
                    'fixedValue' => '0',
                    'fiveYearValue' => '123456',
                    'effectiveFrom' => '2015-01-01'
                ],
            ]
        ];
    }
}
