<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\IrfoPsvFrequency as Sut;

/**
 * IrfoPsvFrequency test
 */
class IrfoPsvFrequencyTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new Sut();
        $query = $bookmark->getQuery(['irfoPsvAuth' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    /**
     * @dataProvider testRenderValidDataProvider
     */
    public function testRender($results, $expected)
    {
        $bookmark = new Sut();
        $bookmark->setData($results);

        $this->assertEquals($expected, $bookmark->render());
    }

    public function testRenderValidDataProvider()
    {
        return [
            [
                [
                    'journeyFrequency' => [
                        'description' => 'daily'
                    ]
                ],
                'daily',
            ],
            [
                [],
                '',
            ]
        ];
    }
}
