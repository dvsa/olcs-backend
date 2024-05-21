<?php

namespace Dvsa\OlcsTest\DocumentShare\Data\Object;

use Dvsa\Olcs\DocumentShare\Data\Object\File;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class DocmanFileTest extends TestCase
{
    protected $vfs;

    public function setUp(): void
    {
        $this->vfs = vfsStream::setup('temp');
    }

    public function testSetContentFromDsStream()
    {
        $res = vfsStream::newFile('res')
            ->at($this->vfs)
            ->url();

        $streamContent = 'UNIT_expect_content' . str_repeat('E', File::CHUNK_SIZE);
        $streamBody =
            str_repeat('x', intval(File::CHUNK_SIZE - strlen('content') / 2)) .
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
        $this->expectException(\Exception::class);

        $res = vfsStream::newFile('res')
            ->at($this->vfs)
            ->url();

        (new File())
            ->setResource($res)
            ->setContentFromDsStream(null);
    }

    public function testSetContentFromDsStreamExcRes404()
    {
        $this->expectException(\Exception::class);

        $stream = vfsStream::newFile('stream')
            ->at($this->vfs)
            ->url();

        (new File())
            ->setResource(null)
            ->setContentFromDsStream($stream);
    }
}
