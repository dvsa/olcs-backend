<?php
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\DiscList;
use Dvsa\Olcs\Api\Service\Document\Parser\RtfParser;

/**
 * Disc list test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DiscListTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new DiscList();
        $query = $bookmark->getQuery([123, 456]);

        $this->assertCount(2, $query);
        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query[0]);
        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query[1]);
    }

    public function testRenderWithNoData()
    {
        $parser = new RtfParser();
        $bookmark = new DiscList();
        $bookmark->setData([]);
        $bookmark->setParser($parser);

        $result = $bookmark->render();

        $this->assertEquals('', $result);
    }

    public function testRenderWithTwoDiscsStillReturnsFullPage()
    {
        $data = [
            [
                'isCopy' => 'N',
                'discNo' => 1,
                'licenceVehicle' => [
                    'licence' => [
                        'organisation' => [
                            'name' => 'A short org name',
                        ],
                        'tradingNames' => [
                            ['name' => 'org'],
                            ['name' => 'trading'],
                            ['name' => 'names']
                        ],
                        'licNo' => 'L1234',
                        'expiryDate' => '2014-10-03'
                    ],
                    'vehicle' => [
                        'vrm' => 'VRM123'
                    ],
                    'interimApplication' => null
                ],
            ],
            /**
             * Data set 2: is a copy, long org name, no expiry date
             */
            [
                'isCopy' => 'Y',
                'discNo' => 2,
                'licenceVehicle' => [
                    'licence' => [
                        'organisation' => [
                            'name' => 'An extremely long org name which will split over multiple lines',
                        ],
                        'tradingNames' => [],
                        'licNo' => 'L3143',
                        'expiryDate' => null
                    ],
                    'vehicle' => [
                        'vrm' => 'VRM321'
                    ],
                    'interimApplication' => null
                ],
            ],
            [
                'isCopy' => 'N',
                'discNo' => 3,
                'licenceVehicle' => [
                    'licence' => [
                        'organisation' => [
                            'name' => 'A short org name',
                        ],
                        'tradingNames' => [
                            ['name' => 'org'],
                            ['name' => 'trading'],
                            ['name' => 'names']
                        ],
                        'licNo' => 'L1234',
                        'expiryDate' => '2014-10-03'
                    ],
                    'vehicle' => [
                        'vrm' => 'VRM123'
                    ],
                    'interimApplication' => [
                        'id' => 1,
                        'interimStart' => '2014-01-01'
                    ]
                ],
            ],
        ];

        $parser = $this->createPartialMock('Dvsa\Olcs\Api\Service\Document\Parser\RtfParser', ['replace']);

        $expectedRowOne = [
            'DISC1_TITLE' => '',
            'DISC1_DISC_NO' => 1,
            'DISC1_LINE1' => 'A short org name',
            'DISC1_LINE2' => '',
            'DISC1_LINE3' => '',
            'DISC1_LINE4' => 'org, trading, names',
            'DISC1_LINE5' => '',
            'DISC1_LICENCE_ID' => 'L1234',
            'DISC1_VEHICLE_REG' => 'VRM123',
            'DISC1_EXPIRY_DATE' => '03-10-2014',

            'DISC2_TITLE' => 'COPY',
            'DISC2_DISC_NO' => 2,
            'DISC2_LINE1' => 'An extremely long org n',
            'DISC2_LINE2' => 'ame which will split ov',
            'DISC2_LINE3' => 'er multiple lines',
            'DISC2_LINE4' => '',
            'DISC2_LINE5' => '',
            'DISC2_LICENCE_ID' => 'L3143',
            'DISC2_VEHICLE_REG' => 'VRM321',
            'DISC2_EXPIRY_DATE' => 'N/A',

            'ROW_HEIGHT' => 2526
        ];

        $expectedRowTwo = [
            'DISC1_TITLE' => 'INTERIM',
            'DISC1_DISC_NO' => 3,
            'DISC1_LINE1' => 'A short org name',
            'DISC1_LINE2' => '',
            'DISC1_LINE3' => '',
            'DISC1_LINE4' => 'org, trading, names',
            'DISC1_LINE5' => '',
            'DISC1_LICENCE_ID' => 'L1234 START 2014-01-01',
            'DISC1_VEHICLE_REG' => 'VRM123',
            'DISC1_EXPIRY_DATE' => '03-10-2014',

            'DISC2_TITLE' => 'XXXXXXXXXX',
            'DISC2_DISC_NO' => 'XXXXXXXXXX',
            'DISC2_LINE1' => 'XXXXXXXXXX',
            'DISC2_LINE2' => 'XXXXXXXXXX',
            'DISC2_LINE3' => 'XXXXXXXXXX',
            'DISC2_LINE4' => 'XXXXXXXXXX',
            'DISC2_LINE5' => 'XXXXXXXXXX',
            'DISC2_LICENCE_ID' => 'XXXXXXXXXX',
            'DISC2_VEHICLE_REG' => 'XXXXXXXXXX',
            'DISC2_EXPIRY_DATE' => 'XXXXXXXXXX',

            'ROW_HEIGHT' => 2526
        ];

        $expectedRowThree = [
            'DISC1_TITLE' => 'XXXXXXXXXX',
            'DISC1_DISC_NO' => 'XXXXXXXXXX',
            'DISC1_LINE1' => 'XXXXXXXXXX',
            'DISC1_LINE2' => 'XXXXXXXXXX',
            'DISC1_LINE3' => 'XXXXXXXXXX',
            'DISC1_LINE4' => 'XXXXXXXXXX',
            'DISC1_LINE5' => 'XXXXXXXXXX',
            'DISC1_LICENCE_ID' => 'XXXXXXXXXX',
            'DISC1_VEHICLE_REG' => 'XXXXXXXXXX',
            'DISC1_EXPIRY_DATE' => 'XXXXXXXXXX',

            'DISC2_TITLE' => 'XXXXXXXXXX',
            'DISC2_DISC_NO' => 'XXXXXXXXXX',
            'DISC2_LINE1' => 'XXXXXXXXXX',
            'DISC2_LINE2' => 'XXXXXXXXXX',
            'DISC2_LINE3' => 'XXXXXXXXXX',
            'DISC2_LINE4' => 'XXXXXXXXXX',
            'DISC2_LINE5' => 'XXXXXXXXXX',
            'DISC2_LICENCE_ID' => 'XXXXXXXXXX',
            'DISC2_VEHICLE_REG' => 'XXXXXXXXXX',
            'DISC2_EXPIRY_DATE' => 'XXXXXXXXXX',

            'ROW_HEIGHT' => 359
        ];

        $parser->expects($this->at(0))
            ->method('replace')
            ->with('snippet', $expectedRowOne)
            ->willReturn('foo');

        $parser->expects($this->at(1))
            ->method('replace')
            ->with('snippet', $expectedRowTwo)
            ->willReturn('bar');

        $parser->expects($this->at(2))
            ->method('replace')
            ->with('snippet', $expectedRowThree)
            ->willReturn('baz');

        $bookmark = $this->createPartialMock('Dvsa\Olcs\Api\Service\Document\Bookmark\DiscList', ['getSnippet']);

        $bookmark->expects($this->any())
            ->method('getSnippet')
            ->willReturn('snippet');

        $bookmark->setData($data);
        $bookmark->setParser($parser);

        $result = $bookmark->render();
        $this->assertEquals('foobarbaz', $result);
    }
}
