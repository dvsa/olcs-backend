<?php

/**
 * Document List Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Document;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Document\DocumentList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\DocumentSearchView as Repo;
use Dvsa\Olcs\Transfer\Query\Document\DocumentList as Qry;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Doctrine\ORM\Query;

/**
 * Document List Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DocumentListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DocumentList();
        $this->mockRepo('DocumentSearchView', Repo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $mockDocument = m::mock(BundleSerializableInterface::class)
            ->shouldReceive('serialize')
            ->andReturn(['foo' => 'bar'])
            ->once()
            ->getMock();

        $this->repoMap['DocumentSearchView']->shouldReceive('fetchList')
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn([$mockDocument])
            ->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(1)
            ->shouldReceive('hasRows')
            ->with(m::type(Qry::class))
            ->andReturn(1);

        $this->assertEquals(
            [
                'result' => [['foo' => 'bar']],
                'count' => 1,
                'count-unfiltered' => 1
            ],
            $this->sut->handleQuery($query)
        );
    }
}
