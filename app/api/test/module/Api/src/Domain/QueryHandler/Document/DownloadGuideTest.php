<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Document;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\Document\DownloadGuide;
use Dvsa\Olcs\Api\Entity\Doc\DocTemplate;
use Dvsa\Olcs\Transfer\Query as TransferQry;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\DocTemplate as DocTemplateRepo;
use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Api\Domain\QueryHandler\Document\DownloadGuide
 */
class DownloadGuideTest extends QueryHandlerTestCase
{
    /** @var  m\MockInterface */
    protected $sut;

    public function setUp()
    {
        $this->sut = m::mock(DownloadGuide::class . '[download, setIsInline]')
            ->shouldAllowMockingProtectedMethods();
        $this->mockRepo('DocTemplate', DocTemplateRepo::class);
        $this->mockedSmServices['config'] = [];
        $this->mockedSmServices['FileUploader'] = m::mock(ContentStoreFileUploader::class);

        parent::setUp();
    }

    public function testHandleQueryTryingToGetIntoParent()
    {
        $this->expectException(NotFoundException::class);

        $this->sut->shouldReceive('setIsInline')->once()->with(true);

        $query = TransferQry\Document\DownloadGuide::create(
            [
                'identifier' => '../file1.pdf',
                'isInline' => true,
            ]
        );

        $this->sut->handleQuery($query);
    }

    public function testHandleQuery()
    {
        $fileName = 'unit_file1.pdf';

        $this->sut
            ->shouldReceive('setIsInline')->once()->with(false)
            ->shouldReceive('download')
            ->once()
            ->with($fileName, '/guides/' . $fileName)
            ->andReturn('EXPECTED');

        $query = TransferQry\Document\DownloadGuide::create(
            [
                'identifier' => $fileName,
                'isInline' => false,
            ]
        );
        $actual = $this->sut->handleQuery($query);

        static::assertEquals('EXPECTED', $actual);
    }

    public function testHandleQueryIsSlug()
    {
        $templateSlug = 'some-template-slug';
        $fileName = 'someFile.txt';

        $docTemplate = m::mock(DocTemplate::class);

        $this->repoMap['DocTemplate']->shouldReceive('fetchByTemplateSlug')
            ->with($templateSlug)
            ->andReturn($docTemplate);

        $docTemplate->shouldReceive('getDocument->getIdentifier')
            ->once()
            ->andReturn($fileName);

        $this->sut
            ->shouldReceive('setIsInline')->once()->with(false)
            ->shouldReceive('download')
            ->once()
            ->with($fileName, '/guides/' . $fileName)
            ->andReturn('EXPECTED');

        $query = TransferQry\Document\DownloadGuide::create(
            [
                'identifier' => $templateSlug,
                'isInline' => false,
                'isSlug' => true
            ]
        );
        $actual = $this->sut->handleQuery($query);

        static::assertEquals('EXPECTED', $actual);
    }
}
