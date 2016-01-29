<?php

/**
 * Note List Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Processing;

use Dvsa\Olcs\Api\Domain\QueryHandler\Processing\NoteList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query\Processing\NoteList as Qry;
use Mockery as m;

/**
 * Note List Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class NoteListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new NoteList();
        $this->mockRepo('Note', Repository\Note::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $data = [
            'case' => null,
            'busReg' => null,
            'application' => null
        ];

        $query = Qry::create($data);

        $this->repoMap['Note']->shouldReceive('disableSoftDeleteable')->once();
        $this->repoMap['Note']->shouldReceive('fetchList')->once()->with($query)->andReturn(['foo' => 'bar']);
        $this->repoMap['Note']->shouldReceive('fetchCount')->with($query)->andReturn(5);
        $this->repoMap['Note']->shouldReceive('hasRows')->with(m::type(Qry::class))->andReturn(1);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'result' => ['foo' => 'bar'],
            'count' => 5,
            'count-unfiltered' => 1
        ];

        $this->assertEquals($expected, $result);
    }
}
