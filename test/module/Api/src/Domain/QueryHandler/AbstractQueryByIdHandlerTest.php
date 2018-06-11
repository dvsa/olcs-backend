<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler;

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Mockery as m;

/**
 * Reusable way to test query handlers using fetchUsingId method
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
abstract class AbstractQueryByIdHandlerTest extends QueryHandlerTestCase
{
    /**
     * Related data also being fetched
     *
     * @var array
     */
    protected $bundle = [];

    /**
     * FQDN of the related doctrine entity
     *
     * @var  string
     */
    protected $entityClass;

    /**
     * FQDN of the query handler being tested
     *
     * @var  string
     */
    protected $sutClass;

    /**
     * FQDN of the query being handled
     *
     * @var  string
     */
    protected $qryClass;

    /**
     * FQDN of the related repo
     *
     * @var  string
     */
    protected $repoClass;

    /**
     * Name of the related repo
     *
     * @var  string
     */
    protected $sutRepo;

    /**
     * Query handler being tested
     *
     * @var QueryHandler\QueryHandlerInterface
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new $this->sutClass();
        $this->mockRepo($this->sutRepo, $this->repoClass);
        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = $this->qryClass::create(['id' => 1]);
        $resultArray = ['result' => ['serialized']];

        $mockEntity = m::mock($this->entityClass)
            ->shouldReceive('serialize')
            ->once()
            ->with($this->bundle)
            ->andReturn($resultArray);

        $this->repoMap[$this->sutRepo]
            ->shouldReceive('fetchUsingId')
            ->with($query)
            ->once()
            ->andReturn($mockEntity);

        self::assertSame(
            $resultArray,
            $this->sut->handleQuery($query)->serialize()
        );
    }
}
