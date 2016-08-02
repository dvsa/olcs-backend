<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Document;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\Document\AbstractDownload;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Dvsa\Olcs\DocumentShare\Data\Object\File as ContentStoreFile;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use org\bovigo\vfs\vfsStream;
use Zend\Http\Response\Stream;

/**
 * @covers Dvsa\Olcs\Api\Domain\QueryHandler\Document\AbstractDownload
 */
class AbstractDownloadTest extends QueryHandlerTestCase
{
    /** @var  AbstractDownloadStub */
    protected $sut;

    public function setUp()
    {
        $this->sut = new AbstractDownloadStub();

        $this->mockedSmServices['FileUploader'] = m::mock(ContentStoreFileUploader::class);

        parent::setUp();
    }

    public function testDownloadFailExcNotFound()
    {
        $this->setExpectedException(NotFoundException::class);

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
    public function testDownload($identifier, $isInline, $expectIsDownload)
    {
        $this->sut->setIsInline($isInline);

        $path = '/unit_dir/unit_file1.pdf';

        $expectContent = 'unit_Contnet';
        $expectMime = 'unit_mime';
        $expectSize = 9999;

        $vfs = vfsStream::setup('temp');
        $tmpFilePath = vfsStream::newFile('stream')->withContent($expectContent)->at($vfs)->url();

        $mockFile = m::mock(ContentStoreFile::class)
            ->shouldReceive('getResource')->once()->andReturn($tmpFilePath)
            ->shouldReceive('getMimeType')->once()->andReturn($expectMime)
            ->shouldReceive('getSize')->once()->andReturn($expectSize)
            ->getMock();

        $this->mockedSmServices['FileUploader']
            ->shouldReceive('download')
            ->once()
            ->with($path)
            ->andReturn($mockFile);

        //  call & check
        $actual = $this->sut->download($identifier, $path);

        static::assertInstanceOf(\Zend\Http\Response\Stream::class, $actual);
        static::assertEquals($tmpFilePath, $actual->getStreamName());
        static::assertEquals($expectContent, $actual->getBody());

        $expectHeaders = [
            'Content-Type' => $expectMime,
            'Content-Length' => $expectSize,
        ];
        if ($expectIsDownload === true) {
            $expectHeaders ['Content-Disposition'] = 'attachment; filename="' . basename($identifier) . '"';
        }
        static::assertEquals($expectHeaders, $actual->getHeaders()->toArray());
    }

    public function dpTestDownload()
    {
        return [
            [
                'identifier' => 'unit_file.ext',
                'isInline' => false,
                'expectIsDownload' => true,
            ],
            [
                'identifier' => 'unit_file.html',
                'isInline' => false,
                'expectIsDownload' => false,
            ],
            [
                'identifier' => 'unit_file.ext',
                'isInline' => true,
                'expectIsDownload' => false,
            ],
        ];
    }
}

/**
 * Stub class of AbstractDownload handler for testing
 */
class AbstractDownloadStub extends AbstractDownload
{
    public function download($identifier, $path = null)
    {
        return parent::download($identifier, $path);
    }

    public function setIsInline($inline)
    {
        return parent::setIsInline($inline);
    }

    public function handleQuery(QueryInterface $query)
    {
    }
}
