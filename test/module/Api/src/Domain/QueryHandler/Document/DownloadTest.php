<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Document;

use Dvsa\Olcs\Api\Domain\QueryHandler\Document\Download;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Dvsa\Olcs\Transfer\Query as TransferQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Api\Domain\QueryHandler\Document\Download
 */
class DownloadTest extends QueryHandlerTestCase
{
    /** @var  m\MockInterface */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = m::mock(Download::class . '[download, setIsInline]')
            ->shouldAllowMockingProtectedMethods();

        $this->mockRepo('Document', \Dvsa\Olcs\Api\Domain\Repository\Document::class);

        $this->mockedSmServices['config'] = [];
        $this->mockedSmServices['FileUploader'] = m::mock(ContentStoreFileUploader::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $identifier = 20062016;

        $query = TransferQry\Document\Download::create(
            [
                'identifier' => $identifier,
                'isInline' => true,
            ]
        );

        $fileName = 'foo/bar/12345.pdf';

        $this->repoMap['Document']
            ->shouldReceive('fetchById')
            ->with($identifier)
            ->once()
            ->andReturn(new Document($fileName));

        $this->sut
            ->shouldReceive('setIsInline')->once()->with(true)
            ->shouldReceive('download')->once()->with($fileName)->andReturn('EXPECTED');

        $actual = $this->sut->handleQuery($query);

        static::assertEquals('EXPECTED', $actual);
    }
}
