<?php

/**
 * Download Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Document;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Document\Download;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Document as DocumentRepo;
use Dvsa\Olcs\Transfer\Query\Document\Document as Qry;

/**
 * Download Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DownloadTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Download();
        $this->mockRepo('Document', DocumentRepo::class);

        $this->mockedSmServices['FileUploader'] = m::mock(ContentStoreFileUploader::class);

        parent::setUp();
    }

    public function testHandleQueryNotFoundInDb()
    {
        $this->setExpectedException(NotFoundException::class);

        $query = \Dvsa\Olcs\Transfer\Query\Document\Download::create(['identifier' => '12345']);

        $documents = [];

        $this->repoMap['Document']->shouldReceive('fetchByIdentifier')
            ->once()
            ->with('12345')
            ->andReturn($documents);

        $this->sut->handleQuery($query);
    }

    public function testHandleQueryNotFoundInStore()
    {
        $this->setExpectedException(NotFoundException::class);

        $query = \Dvsa\Olcs\Transfer\Query\Document\Download::create(['identifier' => '12345']);

        /** @var Document $document */
        $document = m::mock(Document::class)->makePartial();
        $document->setFilename('foo.pdf');
        $document->setIdentifier('12345');

        $documents = [
            $document
        ];

        $this->repoMap['Document']->shouldReceive('fetchByIdentifier')
            ->once()
            ->with('12345')
            ->andReturn($documents);

        $file = null;

        $this->mockedSmServices['FileUploader']->shouldReceive('download')
            ->once()
            ->with('12345')
            ->andReturn($file);

        $this->sut->handleQuery($query);
    }

    public function testHandleQuery()
    {
        $query = \Dvsa\Olcs\Transfer\Query\Document\Download::create(['identifier' => '12345']);

        /** @var Document $document */
        $document = m::mock(Document::class)->makePartial();
        $document->setFilename('/bar/foo.pdf');
        $document->setIdentifier('12345');
        $document->shouldReceive('serialize')
            ->with([])
            ->andReturn(['foo' => 'bar']);

        $documents = [
            $document
        ];

        $this->repoMap['Document']->shouldReceive('fetchByIdentifier')
            ->once()
            ->with('12345')
            ->andReturn($documents);

        $file = m::mock();
        $file->shouldReceive('getContent')
            ->andReturn('<foo>');

        $this->mockedSmServices['FileUploader']->shouldReceive('download')
            ->once()
            ->with('12345')
            ->andReturn($file);

        $result = $this->sut->handleQuery($query);

        $data = $result->serialize();

        $expected = [
            'foo' => 'bar',
            'fileName' => 'foo.pdf',
            'content' => base64_encode('<foo>')
        ];

        $this->assertEquals($expected, $data);
    }
}
