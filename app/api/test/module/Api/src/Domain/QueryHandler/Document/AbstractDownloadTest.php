<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Document;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Dvsa\Olcs\DocumentShare\Data\Object\File as ContentStoreFile;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use org\bovigo\vfs\vfsStream;

/**
 * @covers \Dvsa\Olcs\Api\Domain\QueryHandler\Document\AbstractDownload
 */
class AbstractDownloadTest extends QueryHandlerTestCase
{
    const MIME_TYPE = 'unit_mime';
    const MIME_TYPE_EXCLUDE = 'unit_EXC_mime';

    /** @var  AbstractDownloadStub */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new AbstractDownloadStub();

        $this->mockedSmServices['config'] = [
            'document_share' => [
                'invalid_defined_mime_types' => [
                    'unit_excl_ext' => self::MIME_TYPE_EXCLUDE,
                ],
            ],
        ];
        $this->mockedSmServices['FileUploader'] = m::mock(ContentStoreFileUploader::class);

        parent::setUp();
    }

    public function testDownloadFailExcNotFound()
    {
        $this->expectException(NotFoundException::class);

        $path = '/unit_dir/unit_file1.pdf';

        $this->mockedSmServices['FileUploader']
            ->shouldReceive('download')
            ->once()
            ->with($path)
            ->andReturn(null);

        $this->sut->download($path);
    }

    /**
     * @dataProvider dpTestDownload
     */
    public function testDownload($identifier, $path, $isInline, $expect)
    {
        $this->sut->setIsInline($isInline);

        $expectContent = 'unit_Contnet';
        $expectSize = '9999';

        $vfs = vfsStream::setup('temp');
        $tmpFilePath = vfsStream::newFile('stream')->withContent($expectContent)->at($vfs)->url();

        $mockFile = m::mock(ContentStoreFile::class)
            ->shouldReceive('getResource')->once()->andReturn($tmpFilePath)
            ->shouldReceive('getSize')->once()->andReturn($expectSize)
            ->shouldReceive('getMimeType')
            ->times($expect['mime'] !== self::MIME_TYPE_EXCLUDE ? 1 : 0)
            ->andReturn(self::MIME_TYPE)
            ->getMock();

        $this->mockedSmServices['FileUploader']
            ->shouldReceive('download')
            ->once()
            ->with($expect['path'])
            ->andReturn($mockFile);

        //  call & check
        $actual = $this->sut->download($identifier, $path);

        static::assertInstanceOf(\Laminas\Http\Response\Stream::class, $actual);
        static::assertEquals($tmpFilePath, $actual->getStreamName());
        static::assertEquals($expectContent, $actual->getBody());

        $expectHeaders = [
            'Content-Type' => $expect['mime'].';charset=UTF-8',
            'Content-Length' => $expectSize,
            'Content-Disposition' => ($expect['isDownload'] ? 'attachment' : 'inline') .
                ';filename="' . $expect['filename'] . '"',
        ];
        static::assertEquals($expectHeaders, $actual->getHeaders()->toArray());
    }

    public function dpTestDownload()
    {
        return [
            [
                'identifier' => 'unit_file.ext',
                'path' => '/unit_dir/unit_file1.pdf',
                'isInline' => false,
                'expect' => [
                    'mime' => self::MIME_TYPE,
                    'isDownload' => true,
                    'path' => '/unit_dir/unit_file1.pdf',
                    'filename' => 'unit_file.ext',
                ],
            ],
            [
                'identifier' => 'unit_file.html',
                'path' => null,
                'isInline' => false,
                'expect' => [
                    'mime' => self::MIME_TYPE,
                    'isDownload' => false,
                    'path' => 'unit_file.html',
                    'filename' => 'unit_file.html',
                ],
            ],
            [
                'identifier' => 'dir/dir/unit_file.unit_excl_ext',
                'path' => null,
                'isInline' => false,
                'expect' => [
                    'mime' => self::MIME_TYPE_EXCLUDE,
                    'isDownload' => true,
                    'path' => 'dir/dir/unit_file.unit_excl_ext',
                    'filename' => 'unit_file.unit_excl_ext',
                ],
            ],
            [
                'identifier' => 'unit_file.ext',
                'path' => 'unti_path',
                'isInline' => true,
                'expect' => [
                    'mime' => self::MIME_TYPE,
                    'isDownload' => false,
                    'path' => 'unti_path',
                    'filename' => 'unit_file.ext',
                ],
            ],
            [
                'identifier' => '/foo/bar',
                'path' => null,
                'isInline' => false,
                'expect' => [
                    'mime' => self::MIME_TYPE,
                    'isDownload' => true,
                    'path' => '/foo/bar',
                    'filename' => 'bar.txt',
                ],
            ],
        ];
    }
}
