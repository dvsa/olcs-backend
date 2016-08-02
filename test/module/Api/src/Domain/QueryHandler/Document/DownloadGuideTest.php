<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Document;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\Document\DownloadGuide;
use Dvsa\Olcs\Transfer\Query as TransferQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
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
    }

    public function testHandleQueryTryingToGetIntoParent()
    {
        $this->setExpectedException(NotFoundException::class);

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
}
