<?php

namespace Dvsa\OlcsTest\Api\Service\File;

use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Dvsa\Olcs\Api\Service\File\Exception;
use Dvsa\Olcs\Api\Service\File\MimeNotAllowedException;
use Dvsa\Olcs\DocumentShare\Data\Object\File as DsFile;
use Dvsa\Olcs\DocumentShare\Service\WebDavClient as ContentStoreClient;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;
use Zend\Http\Response;

/**
 * @covers \Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader
 */
class ContentStoreFileUploaderTest extends MockeryTestCase
{
    const IDENTIFIER = 'unit_Identifier';

    /** @var ContentStoreFileUploader */
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
            ->andReturn(true);

        static::assertTrue($this->sut->remove(self::IDENTIFIER));
    }

    public function testUpload()
    {
        $expectContent = 'unit_content';

        $file = new DsFile();
        $file->setContent($expectContent);

        $response = m::mock(Response::class);
        $response->shouldReceive('isSuccess')
            ->andReturn(true);

        $this->mockContentStoreCli->shouldReceive('write')
            ->once()
            ->with(self::IDENTIFIER, m::type(DsFile::class))
            ->andReturn($response);

        //  call & check
        $actual = $this->sut->upload(self::IDENTIFIER, $file);

        static::assertSame($file, $actual);
        static::assertEquals($expectContent, $actual->getContent());
        static::assertEquals(self::IDENTIFIER, $actual->getIdentifier());
    }

    public function testUploadFail()
    {
        $respBody = 'unit_RespBody';

        $this->expectException(Exception::class, sprintf(ContentStoreFileUploader::ERR_UNABLE_UPLOAD, $respBody));

        $response = m::mock(Response::class);
        $response
            ->shouldReceive('isSuccess')->once()->andReturn(false)
            ->shouldReceive('getStatusCode')->once()->andReturn(Response::STATUS_CODE_500)
            ->shouldReceive('getBody')->once()->andReturn($respBody);

        $this->mockContentStoreCli->shouldReceive('write')
            ->once()
            ->andReturn($response);

        $this->sut->upload(self::IDENTIFIER, new DsFile());
    }

    public function testUploadFailMime()
    {
        $this->expectException(MimeNotAllowedException::class);

        $response = m::mock(Response::class);
        $response
            ->shouldReceive('isSuccess')->once()->andReturn(false)
            ->shouldReceive('getStatusCode')->once()->andReturn(Response::STATUS_CODE_415);

        $this->mockContentStoreCli->shouldReceive('write')
            ->once()
            ->andReturn($response);

        $this->sut->upload(self::IDENTIFIER, new DsFile());
    }
}
