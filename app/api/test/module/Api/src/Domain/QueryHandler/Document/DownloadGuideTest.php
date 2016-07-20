<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Document;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Document\DownloadGuide;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Document as DocumentRepo;
use Dvsa\Olcs\Transfer\Query\Document\Document as Qry;

/**
 * @covers \Dvsa\Olcs\Api\Domain\QueryHandler\Document\DownloadGuide
 */
class DownloadGuideTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DownloadGuide();
        $this->mockedSmServices['FileUploader'] = m::mock(ContentStoreFileUploader::class);

        parent::setUp();
    }

    public function testHandleQueryTryingToGetIntoParent()
    {
        $this->setExpectedException(NotFoundException::class);

        $query = \Dvsa\Olcs\Transfer\Query\Document\DownloadGuide::create(['identifier' => '../file1.pdf']);
        $this->sut->handleQuery($query);
    }

    public function testHandleQueryNotFoundInStore()
    {
        $this->setExpectedException(NotFoundException::class);

        $query = \Dvsa\Olcs\Transfer\Query\Document\Download::create(['identifier' => 'file1.pdf']);

        $file = null;
        $this->mockedSmServices['FileUploader']->shouldReceive('download')
            ->once()
            ->with('/guides/file1.pdf')
            ->andReturn($file);

        $this->sut->handleQuery($query);
    }

    public function testHandleQuery()
    {
        $query = \Dvsa\Olcs\Transfer\Query\Document\DownloadGuide::create(['identifier' => 'file1.pdf']);

        $file = m::mock();
        $file->shouldReceive('getContent')
            ->andReturn('<foo>');

        $this->mockedSmServices['FileUploader']->shouldReceive('download')
            ->once()
            ->with('/guides/file1.pdf')
            ->andReturn($file);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'fileName' => 'file1.pdf',
            'content' => base64_encode('<foo>')
        ];

        $this->assertEquals($expected, $result);
    }
}
