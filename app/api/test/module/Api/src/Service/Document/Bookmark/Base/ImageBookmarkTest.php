<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark\Base;

use Dvsa\OlcsTest\Api\Service\Document\Bookmark\Base\Stub\ImageBookmarkStub;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Dvsa\Olcs\Api\Service\Document\Bookmark\Base\ImageBookmark
 */
class ImageBookmarkTest extends MockeryTestCase
{
    public function testGetImageFail()
    {
        $name = 'unit_Name';

        $mockFs = m::mock();
        $mockFs->shouldReceive('read')->andReturn(null);

        /** @var ImageBookmarkStub|m\MockInterface $sut */
        $sut = new ImageBookmarkStub;
        $sut->setFileStore($mockFs);

        //  expect
        static::expectException(
            \RuntimeException::class,
            'Image path /templates/Image/' . $name . '.jpg does not exist'
        );

        //  call
        $sut->getImage($name);
    }

    public function testGetImageOk()
    {
        if (!function_exists('imagecreate')) {
            static::markTestSkipped('imagecreate, part of gdlib, required for the test to run');
        }

        $name = 'unit_Name';

        $expectWidth = 21;
        $expectHeight = 11;

        $mockImage = imagecreate($expectWidth, $expectHeight);
        ob_start();
        imagejpeg($mockImage);
        $expectContent =  ob_get_contents();
        ob_end_clean();

        $mockFile = m::mock()
            ->shouldReceive('getContent')
            ->with()
            ->andReturn($expectContent)
            ->getMock();

        $mockFs = m::mock()
            ->shouldReceive('read')
            ->with('/templates/Image/' . $name . '.jpg')
            ->andReturn($mockFile)
            ->getMock();

        /** @var \Dvsa\Olcs\Api\Service\Document\Parser\ParserInterface|m\MockInterface $mockParser */
        $mockParser = m::mock()
            ->shouldReceive('renderImage')
            ->with($expectContent, $expectWidth, $expectHeight, 'jpeg')
            ->andReturn('EXPECTED')
            ->getMock();

        /** @var ImageBookmarkStub $sut */
        $sut = new ImageBookmarkStub;
        $sut->setParser($mockParser);
        $sut->setFileStore($mockFs);

        static::assertEquals('EXPECTED', $sut->getImage($name));
    }
}
