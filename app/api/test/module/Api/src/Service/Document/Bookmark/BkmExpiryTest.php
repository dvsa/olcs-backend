<?php

/**
 * BkmExpiry Test
 */
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\BkmExpiry as Sut;

/**
 * BkmExpiry Test
 */
class BkmExpiryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new Sut();
        $query = $bookmark->getQuery(['irfoPsvAuth' => 123]);
        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender($data, $expected)
    {
        $bookmark = new Sut();
        $bookmark->setData($data);

        $this->assertEquals($expected, $bookmark->render());
    }

    public function renderDataProvider()
    {
        return [
            [
                [
                    'expiryDate' => '30.12.2015'
                ],
                '30 December 2015'
            ],
            [
                [],
                ''
            ],
        ];
    }
}
