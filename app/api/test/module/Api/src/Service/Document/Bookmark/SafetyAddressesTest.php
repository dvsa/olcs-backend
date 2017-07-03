<?php
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\SafetyAddresses;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * SafetyAddresses bookmark test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class SafetyAddressesTest extends MockeryTestCase
{
    public function testGetQuery()
    {
        $bookmark = new SafetyAddresses();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRenderWithNoSafetyAddresses()
    {
        $bookmark = new SafetyAddresses();
        $bookmark->setData([]);

        $this->assertEquals(
            '',
            $bookmark->render()
        );
    }

    public function testRenderWithSafetyAddresses()
    {
        /** @var SafetyAddresses|m\Mock $bookmark */
        $bookmark = m::mock('Dvsa\Olcs\Api\Service\Document\Bookmark\SafetyAddresses')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getSnippet')
            ->with('SafetyAddresses')
            ->andReturn('snippet')
            ->once()
            ->getMock();

        $bookmark->setData(
            [
                'workshops' => [
                    [
                        'isExternal' => 'Y',
                        'contactDetails' => [
                            'fao' => 'C Surname',
                            'address' => [
                                'addressLine1' => 'al1',
                                'addressLine2' => 'al2',
                                'addressLine3' => 'al3',
                                'addressLine4' => 'al4',
                                'town' => 'town',
                                'postcode' => 'postcode'
                            ]
                        ]
                    ],
                    [
                        'isExternal' => 'N',
                        'contactDetails' => [
                            'fao' => 'C Surname',
                            'address' => [
                                'addressLine1' => 'al1',
                                'addressLine2' => 'al2',
                                'addressLine3' => 'al3',
                                'addressLine4' => 'al4',
                                'town' => 'town',
                                'postcode' => 'postcode'
                            ]
                        ]
                    ],
                    [
                        'isExternal' => 'Y',
                        'contactDetails' => [
                            'fao' => 'A Surname',
                            'address' => [
                                'addressLine1' => 'al1',
                                'addressLine2' => 'al2',
                                'addressLine3' => 'al3',
                                'addressLine4' => 'al4',
                                'town' => 'town',
                                'postcode' => 'postcode'
                            ]
                        ]
                    ],
                    [
                        'isExternal' => 'N',
                        'contactDetails' => [
                            'fao' => 'B Surname',
                            'address' => [
                                'addressLine1' => 'al1',
                                'addressLine2' => 'al2',
                                'addressLine3' => 'al3',
                                'addressLine4' => 'al4',
                                'town' => 'town',
                                'postcode' => 'postcode'
                            ]
                        ]
                    ],
                ]
            ]
        );

        $row1 = [
            'Address' => 'A Surname, al1, al2, al3, al4, town, postcode',
            'checkbox1' => '',
            'checkbox2' => 'X'
        ];
        $row2 = [
            'Address' => 'B Surname, al1, al2, al3, al4, town, postcode',
            'checkbox1' => 'X',
            'checkbox2' => ''
        ];
        $row3 = [
            'Address' => 'C Surname, al1, al2, al3, al4, town, postcode',
            'checkbox1' => 'X',
            'checkbox2' => ''
        ];
        $row4 = [
            'Address' => 'C Surname, al1, al2, al3, al4, town, postcode',
            'checkbox1' => '',
            'checkbox2' => 'X'
        ];

        $mockParser = m::mock('Dvsa\Olcs\Api\Service\Document\Parser\RtfParser')
            ->shouldReceive('replace')
            ->with('snippet', $row1)
            ->andReturn('row1|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row2)
            ->andReturn('row2|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row3)
            ->andReturn('row3|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row4)
            ->andReturn('row4|')
            ->once()
            ->getMock();

        $bookmark->setParser($mockParser);

        $rendered = 'row1|row2|row3|row4|';
        $this->assertEquals(
            $rendered,
            $bookmark->render()
        );
    }
}
