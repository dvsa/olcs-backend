<?php

/**
 * Content Store File Uploader Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Service\File;

use Dvsa\Olcs\Api\Service\File\Exception;
use Dvsa\Olcs\Api\Service\File\File;
use Dvsa\Olcs\Api\Service\File\MimeNotAllowedException;
use Dvsa\Olcs\DocumentShare\Service\Client;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use OlcsTest\Bootstrap;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceManager;

/**
 * Content Store File Uploader Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ContentStoreFileUploaderTest extends MockeryTestCase
{
    /**
     * @var ContentStoreFileUploader
     */
    protected $sut;

    protected $contentStore;

    public function setUp()
    {
        $this->sut = new ContentStoreFileUploader();

        $this->contentStore = m::mock(Client::class);

        $sm = Bootstrap::getServiceManager();
        $sm->setService('ContentStore', $this->contentStore);

        $this->assertSame($this->sut, $this->sut->createService($sm));
    }

    public function testDownload()
    {
        $this->contentStore->shouldReceive('read')
            ->once()
            ->with('12345')
            ->andReturn('file content');

        $this->assertEquals('file content', $this->sut->download('12345'));
    }

    public function testRemove()
    {
        $this->contentStore->shouldReceive('remove')
            ->once()
            ->with('12345')
            ->andReturn(true);

        $this->assertTrue($this->sut->remove('12345'));
    }

    public function testUpload()
    {
        $file = [
            'content' => 'foo'
        ];

        $this->sut->setFile($file);

        $fileObject = $this->sut->getFile();

        $this->assertInstanceOf(File::class, $fileObject);
        $this->assertEquals('foo', $fileObject->getContent());

        $response = m::mock(Response::class);
        $response->shouldReceive('isSuccess')
            ->andReturn(true);

        $this->contentStore->shouldReceive('write')
            ->once()
            ->with('12345', m::type(\Dvsa\Olcs\DocumentShare\Data\Object\File::class))
            ->andReturn($response);

        $savedFile = $this->sut->upload('12345');

        $this->assertSame($fileObject, $savedFile);
        $this->assertEquals('foo', $savedFile->getContent());
        $this->assertEquals('12345', $savedFile->getIdentifier());
        $this->assertEquals('12345', $savedFile->getPath());
    }

    public function testUploadFail()
    {
        $this->setExpectedException(Exception::class);

        $file = [
            'content' => 'foo'
        ];

        $this->sut->setFile($file);

        $fileObject = $this->sut->getFile();

        $this->assertInstanceOf(File::class, $fileObject);
        $this->assertEquals('foo', $fileObject->getContent());

        $response = m::mock(Response::class);
        $response->shouldReceive('isSuccess')
            ->andReturn(false)
            ->shouldReceive('getStatusCode')
            ->andReturn(500)
            ->shouldReceive('getBody')
            ->andReturn('body');

        $this->contentStore->shouldReceive('write')
            ->once()
            ->with('12345', m::type(\Dvsa\Olcs\DocumentShare\Data\Object\File::class))
            ->andReturn($response);

        $this->sut->upload('12345');
    }

    public function testUploadFailMime()
    {
        $this->setExpectedException(MimeNotAllowedException::class);

        $file = [
            'content' => 'foo'
        ];

        $this->sut->setFile($file);

        $fileObject = $this->sut->getFile();

        $this->assertInstanceOf(File::class, $fileObject);
        $this->assertEquals('foo', $fileObject->getContent());

        $response = m::mock(Response::class);
        $response->shouldReceive('isSuccess')
            ->andReturn(false)
            ->shouldReceive('getStatusCode')
            ->andReturn(415)
            ->shouldReceive('getBody')
            ->andReturn('body');

        $this->contentStore->shouldReceive('write')
            ->once()
            ->with('12345', m::type(\Dvsa\Olcs\DocumentShare\Data\Object\File::class))
            ->andReturn($response);

        $this->sut->upload('12345');
    }
}
