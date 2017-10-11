<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\BrLogo;

/**
 * Br Logo test
 */
class BrLogoTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new BrLogo();

        $this->assertInstanceOf(
            \Dvsa\Olcs\Transfer\Query\QueryInterface::class,
            $bookmark->getQuery(['busRegId' => 123])
        );
        $this->assertTrue(is_null($bookmark->getQuery([])));
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender($data, $image)
    {
        $bookmark = new BrLogo();
        $bookmark->setData($data);

        if (!function_exists('imagecreate')) {
            // to test BrLogo bookmark we need to mock the image,
            // to create the image we need to use imagecreate method (part of gdlib)
            $this->markTestSkipped('imagecreate, part of gdlib, required for the test to run');
        }

        $mockImage = imagecreate(100, 100);
        ob_start();
        imagejpeg($mockImage);
        $content =  ob_get_contents();
        ob_end_clean();

        $fileMock = $this->createPartialMock('\stdClass', ['getContent']);
        $fileMock->expects($this->exactly(!empty($image) ? 1 : 0))
            ->method('getContent')
            ->willReturn($content);

        $fileStoreMock = $this->createPartialMock('\stdClass', ['read']);
        $fileStoreMock->expects($this->exactly(!empty($image) ? 1 : 0))
            ->method('read')
            ->willReturn($fileMock);

        $parserMock = $this->createPartialMock('\stdClass', ['renderImage']);
        $parserMock->expects($this->exactly(!empty($image) ? 1 : 0))
            ->method('renderImage')
            ->with($content, 100, $bookmark::CONTAINER_HEIGHT, 'jpeg')
            ->willReturn('an image');

        $bookmark->setFileStore($fileStoreMock);
        $bookmark->setParser($parserMock);

        $this->assertEquals(
            !empty($image) ? 'an image' : '',
            $bookmark->render()
        );
    }

    public function renderDataProvider()
    {
        return [
            [
                // Scotland
                [
                    'licence' => [
                        'trafficArea' => [
                            'isScotland' => true
                        ]
                    ]
                ],
                'TC_LOGO_SCOTTISH'
            ],
            [
                // Other
                [
                    'licence' => [
                        'trafficArea' => [
                            'isScotland' => false
                        ]
                    ]
                ],
                'TC_LOGO_OTHER'
            ],
            [
                // no data
                [],
                null
            ],
        ];
    }
}
