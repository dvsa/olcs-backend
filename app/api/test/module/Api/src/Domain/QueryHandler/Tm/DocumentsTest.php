<?php

/**
 * Documents Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Tm;

use Dvsa\Olcs\Api\Domain\QueryHandler\Tm\Documents as QueryHandler;
use Dvsa\Olcs\Transfer\Query\Tm\Documents as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Document as DocumentRepo;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Mockery as m;

/**
 * Documents Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DocumentsTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Document', DocumentRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['id' => 1]);

        $mockDocument = m::mock(BundleSerializableInterface::class)
            ->shouldReceive('serialize')
            ->andReturn('foo')
            ->once()
            ->getMock();

        $this->repoMap['Document']
            ->shouldReceive('fetchListForTm')
            ->with(1)
            ->once()
            ->andReturn([$mockDocument])
            ->getMock();

        $this->assertSame(
            [
                'result'    => ['foo'],
                'count'     => 1,
            ],
            $this->sut->handleQuery($query)
        );
    }
}
