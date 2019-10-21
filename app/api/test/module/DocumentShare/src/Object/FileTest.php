<?php

namespace Dvsa\OlcsTest\DocumentShare\Data\Object;

use Dvsa\Olcs\DocumentShare\Data\Object\File;
use org\bovigo\vfs\vfsStream;

/**
 * @covers \Dvsa\Olcs\DocumentShare\Data\Object\File
 */
class FileTest extends \PHPUnit\Framework\TestCase
{
    /** @var  \org\bovigo\vfs\vfsStreamDirectory */
    private $vfs;

    public function setUp()
    {
        $this->vfs = vfsStream::setup('temp');
    }

    public function testSetGet()
    {
        $sut = new File();

        //  check set/get content
        $content = '<html></html>';

        static::assertEquals($sut, $sut->setContent($content));
        static::assertEquals($content, $sut->getContent());

        //  check mime
        static::assertEquals('text/html', $sut->getMimeType());

        //  check size
        static::assertEquals(strlen($content), $sut->getSize());
    }

    public function testSetResource()
    {
        $fsFilePath1 = vfsStream::newFile('unitTemp1')->at($this->vfs)->url();
        $fsFilePath2 = vfsStream::newFile('unitTemp2')->at($this->vfs)->url();

        $sut = new File();

        //  assing resource
        static::assertEquals($sut, $sut->setResource($fsFilePath1));
        static::assertEquals($fsFilePath1, $sut->getResource());

        static::assertTrue(is_file($fsFilePath1));
        static::assertTrue(is_file($fsFilePath2));

        //  set other resource, previous one should be removed
        $sut->setResource($fsFilePath2);
        static::assertEquals($fsFilePath2, $sut->getResource());
        static::assertFalse(is_file($fsFilePath1));
        static::assertTrue(is_file($fsFilePath2));

        //  check file removed when destroy object
        unset($sut);
        static::assertFalse(is_file($fsFilePath2));
    }

    public function testSetContentFromDsStream()
    {
        $res = vfsStream::newFile('res')
            ->at($this->vfs)
            ->url();

        $streamContent = 'UNIT_expect_content' . str_repeat('E', File::CHUNK_SIZE);
        $streamBody =
            str_repeat('x', File::CHUNK_SIZE - strlen('content') / 2) .
            '"content":"' . base64_encode($streamContent) . '"' .
            str_repeat('aftercontent', 3);

        $stream = vfsStream::newFile('stream')
            ->withContent($streamBody)
            ->at($this->vfs)
            ->url();

        $sut = new File();
        $sut->setResource($res);
        $sut->setContentFromDsStream($stream);

        static::assertEquals($streamContent, $sut->getContent());
    }

    public function testSetContentFromDsStreamExcStream404()
    {
        $this->expectException(\Exception::class, File::ERR_CANT_OPEN_DOWNLOAD_STREAM);

        $res = vfsStream::newFile('res')
            ->at($this->vfs)
            ->url();

        (new File())
            ->setResource($res)
            ->setContentFromDsStream(null);
    }

    public function testSetContentFromDsStreamExcRes404()
    {
        $this->expectException(\Exception::class, File::ERR_CANT_OPEN_RES);

        $stream = vfsStream::newFile('stream')
            ->at($this->vfs)
            ->url();

        (new File())
            ->setResource(null)
            ->setContentFromDsStream($stream);
    }
}
