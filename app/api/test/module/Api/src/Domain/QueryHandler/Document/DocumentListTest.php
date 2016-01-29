<?php

/**
 * Document List Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Document;

use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Document\DocumentList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\DocumentSearchView as Repo;
use Dvsa\Olcs\Transfer\Query\Document\DocumentList as Qry;

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

        $this->repoMap['DocumentSearchView']->shouldReceive('fetchList')
            ->with($query)
            ->andReturn(['foo' => 'bar'])
            ->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(10)
            ->shouldReceive('hasRows')
            ->with(m::type(Qry::class))
            ->andReturn(1);

        $this->assertEquals(
            [
                'result' => ['foo' => 'bar'],
                'count' => 10,
                'count-unfiltered' => 1
            ],
            $this->sut->handleQuery($query)
        );
    }
}
