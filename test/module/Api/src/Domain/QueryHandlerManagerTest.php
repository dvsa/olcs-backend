<?php

/**
 * Query Handler Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\Api\Domain;

use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\Exception\RuntimeException;

/**
 * Query Handler Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueryHandlerManagerTest extends MockeryTestCase
{
    /**
     * @var QueryHandlerManager
     */
    private $sut;

    public function setUp()
    {
        $config = m::mock(ConfigInterface::class);
        $config->shouldReceive('configureServiceManager')->with(m::type(QueryHandlerManager::class));

        $this->sut = new QueryHandlerManager($config);
    }

    public function testHandleQuery()
    {
        $query = m::mock(QueryInterface::class)->makePartial();
        $query->shouldReceive('getArrayCopy')->once()->andReturn(['foo' => 'bar']);

        $mockService = m::mock(QueryHandlerInterface::class);
        $mockService->shouldReceive('handleQuery')->with($query)->andReturn(['response']);

        $this->sut->setService(get_class($query), $mockService);

        $this->assertEquals(['response'], $this->sut->handleQuery($query));
    }

    public function testHandleQueryInvalid()
    {
        $this->setExpectedException(RuntimeException::class);

        $query = m::mock(QueryInterface::class)->makePartial();

        $mockService = m::mock();
        $mockService->shouldReceive('handleQuery')->with($query)->andReturn(['response']);

        $this->sut->setService(get_class($query), $mockService);

        $this->sut->handleQuery($query);
    }
}
