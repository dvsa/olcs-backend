<?php

/**
 * Query Handler Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\Api\Domain;

use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\HandlerInterface;
use Dvsa\Olcs\Api\Domain\ValidationHandlerManager;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\ConfigInterface;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\RuntimeException;
use Laminas\ServiceManager\ServiceManager;

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

    private $vhm;

    public function setUp(): void
    {
        $this->vhm = m::mock(ValidationHandlerManager::class)->makePartial();

        $sm = m::mock(ServiceManager::class)->makePartial();
        $sm->setService('ValidationHandlerManager', $this->vhm);

        $config = m::mock(ConfigInterface::class);
        $config->shouldReceive('configureServiceManager')->with(m::type(QueryHandlerManager::class));

        $this->sut = new QueryHandlerManager($config);
        $this->sut->setServiceLocator($sm);
    }

    public function testHandleQuery()
    {
        $query = m::mock(QueryInterface::class)->makePartial();
        $query->shouldReceive('getArrayCopy')->once()->andReturn(['foo' => 'bar']);

        $mockService = m::mock(QueryHandlerInterface::class);
        $mockService->shouldReceive('checkEnabled')->once()->andReturn(true);
        $mockService->shouldReceive('handleQuery')->with($query)->andReturn(['response']);

        $mockValidator = m::mock(HandlerInterface::class);
        $mockValidator->shouldReceive('isValid')->with($query)->andReturn(true);
        $this->vhm->setService(get_class($mockService), $mockValidator);

        $this->sut->setService(get_class($query), $mockService);

        $this->assertEquals(['response'], $this->sut->handleQuery($query, true));
    }

    public function testHandleQueryReturningEntity()
    {
        $query = m::mock(QueryInterface::class)->makePartial();
        $query->shouldReceive('getArrayCopy')->once()->andReturn(['foo' => 'bar']);

        $response = m::mock(\Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface::class);
        $response->shouldReceive('serialize')->with()->once()->andReturn(['response']);

        $mockService = m::mock(QueryHandlerInterface::class);
        $mockService->shouldReceive('checkEnabled')->once()->andReturn(true);
        $mockService->shouldReceive('handleQuery')->with($query)->andReturn($response);

        $mockValidator = m::mock(HandlerInterface::class);
        $mockValidator->shouldReceive('isValid')->with($query)->andReturn(true);
        $this->vhm->setService(get_class($mockService), $mockValidator);

        $this->sut->setService(get_class($query), $mockService);

        $this->assertEquals($response, $this->sut->handleQuery($query, true));
    }

    public function testHandleQueryFailingValidator()
    {
        $this->expectException(ForbiddenException::class);

        $query = m::mock(QueryInterface::class)->makePartial();
        $query->shouldReceive('getArrayCopy')->twice()->andReturn(['foo' => 'bar']);

        $mockService = m::mock(QueryHandlerInterface::class);
        $mockService->shouldReceive('checkEnabled')->once()->andReturn(true);
        $mockService->shouldReceive('handleQuery')->never();

        $mockValidator = m::mock(HandlerInterface::class);
        $mockValidator->shouldReceive('isValid')->with($query)->andReturn(false);
        $this->vhm->setService(get_class($mockService), $mockValidator);

        $this->sut->setService(get_class($query), $mockService);

        $this->sut->handleQuery($query, true);
    }

    public function testHandleQueryInvalid()
    {
        $this->expectException(RuntimeException::class);

        $query = m::mock(QueryInterface::class)->makePartial();

        $mockService = m::mock();
        $mockService->shouldReceive('handleQuery')->with($query)->andReturn(['response']);

        $this->sut->setService(get_class($query), $mockService);

        $this->sut->handleQuery($query);
    }

    public function testValidate()
    {
        $plugin = m::mock(QueryHandlerInterface::class);

        $this->assertNull($this->sut->validate($plugin));
    }

    public function testValidateInvalid()
    {
        $this->expectException(InvalidServiceException::class);

        $this->sut->validate(null);
    }

    /**
     * @todo To be removed as part of OLCS-28149
     */
    public function testValidatePlugin()
    {
        $plugin = m::mock(QueryHandlerInterface::class);

        $this->assertNull($this->sut->validatePlugin($plugin));
    }

    /**
     * @todo To be removed as part of OLCS-28149
     */
    public function testValidatePluginInvalid()
    {
        $this->expectException(RuntimeException::class);

        $this->sut->validatePlugin(null);
    }
}
