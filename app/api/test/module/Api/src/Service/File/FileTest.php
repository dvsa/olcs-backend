<?php

namespace Dvsa\OlcsTest\Api\Service\File;

use Dvsa\Olcs\Api\Service\File\File;
use org\bovigo\vfs\vfsStream;

/**
 * @covers Dvsa\Olcs\Api\Service\File\File
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    /** @var  File */
    private $sut;

    public function setUp()
    {
        $this->sut = new File();
    }

    public function testGetSet()
    {
        $identifier = 'unit_Identifier';
        static::assertEquals($this->sut, $this->sut->setIdentifier($identifier));
        static::assertEquals($identifier, $this->sut->getIdentifier());

        $path = 'unit_Path';
        static::assertEquals($this->sut, $this->sut->setPath($path));
        static::assertEquals($path, $this->sut->getPath());

        $name = 'unit_Name.extX';
        static::assertEquals($this->sut, $this->sut->setName($name));
        static::assertEquals($name, $this->sut->getName());
        static::assertEquals('extX', $this->sut->getExtension());

        $content = '<html></html>';
        static::assertEquals(
            $this->sut,
            $this->sut->setContent($content)
        );
        static::assertEquals($content, $this->sut->getContent());
        static::assertEquals('text/html', $this->sut->getMimeType());
        static::assertEquals(13, $this->sut->getSize());
    }

    /**
     * @dataProvider dpTestGetExtention
     */
    public function testGetExtention($name, $expect)
    {
        $this->sut->setName($name);
        static::assertEquals($expect, $this->sut->getExtension());
    }

    public function dpTestGetExtention()
    {
        return [
            [
                'name'=> '',
                'expect' => '',
            ],
            [
                'name'=> 'A file with no extension',
                'expect' => '',
            ],
            [
                'name'=> 'Another file.txt',
                'expect' => 'txt',
            ],
            [
                'name'=> 'article.jpg.doc',
                'expect' => 'doc',
            ],
        ];
    }

    public function testSetContentFromFileData()
    {
        $content = 'test content inside test file';

        $vfs = vfsStream::setup('temp');
        $fsFilePath = vfsStream::newFile('unitTemp')
            ->withContent($content)
            ->at($vfs)
            ->url();

        $file = new File();
        $file->setContent(
            [
                'tmp_name' => $fsFilePath,
            ]
        );

        static::assertEquals($content, $file->getContent());
    }
}
