<?php

namespace Dvsa\OlcsTest\Api\Service\File;

use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Dvsa\Olcs\Api\Service\File\Exception;
use Dvsa\Olcs\Api\Service\File\File;
use Dvsa\Olcs\Api\Service\File\MimeNotAllowedException;
use Dvsa\Olcs\DocumentShare\Data\Object\File as ContentStoreFile;
use Dvsa\Olcs\DocumentShare\Service\Client as ContentStoreClient;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;
use Zend\Http\Response;

/**
 * @covers Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader
 */
class ContentStoreFileUploaderTest extends MockeryTestCase
{
    const IDENTIFIER = 'unit_Identifier';

    /**
     * @var ContentStoreFileUploader
     */
    protected $sut;

    /** @var m\MockInterface  */
    protected $mockContentStoreCli;

    public function setUp()
    {
        $this->sut = new ContentStoreFileUploader();

        $this->mockContentStoreCli = m::mock(ContentStoreClient::class);

        $sm = Bootstrap::getServiceManager();
        $sm->setService('ContentStore', $this->mockContentStoreCli);

        static::assertSame($this->sut, $this->sut->createService($sm));
    }

    public function testSetGet()
    {
        $file = new  File();

        $this->sut->setFile($file);

        static::assertSame($file, $this->sut->getFile());
    }

    public function testDownload()
    {
        $this->mockContentStoreCli->shouldReceive('read')
            ->once()
            ->with(self::IDENTIFIER)
            ->andReturn('EXPECT');

        static::assertEquals('EXPECT', $this->sut->download(self::IDENTIFIER));
    }

    public function testRemove()
    {
        $this->mockContentStoreCli->shouldReceive('remove')
            ->once()
            ->with(self::IDENTIFIER)
            ->andReturn('EXPECT');

        static::assertEquals('EXPECT', $this->sut->remove(self::IDENTIFIER));
    }

    public function testUpload()
    {
        $expectContent = 'unit_content';

        $file = new  File();
        $file->setContent($expectContent);

        $this->sut->setFile($file);

        $response = m::mock(Response::class);
        $response->shouldReceive('isSuccess')
            ->andReturn(true);

        $this->mockContentStoreCli->shouldReceive('write')
            ->once()
            ->with(self::IDENTIFIER, m::type(ContentStoreFile::class))
            ->andReturn($response);

        //  call & check
        $actual = $this->sut->upload(self::IDENTIFIER);

        static::assertSame($file, $actual);
        static::assertEquals($expectContent, $actual->getContent());
        static::assertEquals(self::IDENTIFIER, $actual->getIdentifier());
        static::assertEquals(self::IDENTIFIER, $actual->getPath());
    }

    public function testUploadFail()
    {
        $respBody = 'unit_RespBody';

        $this->setExpectedException(Exception::class, sprintf(ContentStoreFileUploader::ERR_UNABLE_UPLOAD, $respBody));

        $response = m::mock(Response::class);
        $response
            ->shouldReceive('isSuccess')->once()->andReturn(false)
            ->shouldReceive('getStatusCode')->once()->andReturn(Response::STATUS_CODE_500)
            ->shouldReceive('getBody')->once()->andReturn($respBody);

        $this->mockContentStoreCli->shouldReceive('write')
            ->once()
            ->andReturn($response);

        $this->sut->setFile(new File());
        $this->sut->upload(self::IDENTIFIER);
    }

    public function testUploadFailMime()
    {
        $this->setExpectedException(MimeNotAllowedException::class);

        $response = m::mock(Response::class);
        $response
            ->shouldReceive('isSuccess')->once()->andReturn(false)
            ->shouldReceive('getStatusCode')->once()->andReturn(Response::STATUS_CODE_415);

        $this->mockContentStoreCli->shouldReceive('write')
            ->once()
            ->andReturn($response);

        $this->sut->setFile(new File());
        $this->sut->upload(self::IDENTIFIER);
    }
}
