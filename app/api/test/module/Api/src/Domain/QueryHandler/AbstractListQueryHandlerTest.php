<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Transfer\Query as TransferQry;
use Mockery as m;

/**
 * @author Dmitry Golubev <d.e.golubev@gmail.com>
 */
abstract class AbstractListQueryHandlerTest extends QueryHandlerTestCase
{
    /** @var  string */
    protected $sutClass;

    /** @var  string */
    protected $qryClass;

    /** @var  string */
    protected $repoClass;

    /** @var  string */
    protected $sutRepo;

    /** @var QueryHandler\QueryHandlerInterface */
    protected $sut;

    public function setUp()
    {
        $this->sut = new $this->sutClass();

        $this->mockRepo($this->sutRepo, $this->repoClass);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        /**
         * @var TransferQry\QueryInterface $qry
         * @var TransferQry\QueryInterface $query
         *
         * Once we're on PHP 7 we can switch to $query = $this->qryClass::create([]); instead
         */
        $qry = $this->qryClass;
        $query = $qry::create([]);

        $mockResult = m::mock();
        $mockResult->shouldReceive('serialize')->once()->andReturn('EXPECT');

        $this->repoMap[$this->sutRepo]
            ->shouldReceive('fetchList')->with($query, Query::HYDRATE_OBJECT)->andReturn([$mockResult])
            ->shouldReceive('fetchCount')->with($query)->andReturn(2);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(2, $result['count']);
        $this->assertEquals(['EXPECT'], $result['result']);
    }
}
