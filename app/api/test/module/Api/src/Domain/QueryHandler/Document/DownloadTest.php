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
        $this->mockedSmServices['FileUploader'] = m::mock(ContentStoreFileUploader::class);

        parent::setUp();
    }

    public function testHandleQueryNotFoundInStore()
    {
        $this->setExpectedException(NotFoundException::class);

        $query = \Dvsa\Olcs\Transfer\Query\Document\Download::create(['identifier' => 'foo/bar/12345.pdf']);

        $file = null;
        $this->mockedSmServices['FileUploader']->shouldReceive('download')
            ->once()
            ->with('foo/bar/12345.pdf')
            ->andReturn($file);

        $this->sut->handleQuery($query);
    }

    public function testHandleQuery()
    {
        $query = \Dvsa\Olcs\Transfer\Query\Document\Download::create(['identifier' => 'foo/bar/12345.pdf']);

        $file = m::mock();
        $file->shouldReceive('getContent')
            ->andReturn('<foo>');

        $this->mockedSmServices['FileUploader']->shouldReceive('download')
            ->once()
            ->with('foo/bar/12345.pdf')
            ->andReturn($file);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'fileName' => '12345.pdf',
            'content' => base64_encode('<foo>')
        ];

        $this->assertEquals($expected, $result);
    }
}
