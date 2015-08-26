<?php


namespace Dvsa\OlcsTest\Api\Controller;

use Dvsa\Olcs\Api\Controller\GenericController;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\Application\UpdateTypeOfLicence;
use Dvsa\Olcs\Api\Domain\CommandHandler\CommandHandlerInterface;
use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;
use Dvsa\Olcs\Api\Mvc\Controller\Plugin\Response;
use Dvsa\Olcs\Transfer\Query\Application\Application;
use Dvsa\OlcsTest\Api\MockLoggerTrait;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Zend\Log\Logger;
use Zend\Mvc\Controller\Plugin\Params;
use Zend\Mvc\Controller\PluginManager;
use Zend\View\Model\JsonModel;
use Dvsa\Olcs\Api\Domain\Exception;

/**
 * Class GenericControllerTest
 */
class GenericControllerTest extends TestCase
{
    use MockLoggerTrait;

    public function testGet()
    {
        $viewModel = new JsonModel();
        $application = new Application();
        $data = ['foo' => 'var'];

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('singleResult')->with($data)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockQueryHandler = m::mock(QueryHandlerInterface::class);
        $mockQueryHandler->shouldReceive('handleQuery')->with($application)->andReturn($data);

        $mockLogger = $this->mockLogger();

        $mockSl = $this->getMockSl($mockResponse, $mockParams, $mockQueryHandler, 'QueryHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->get(25);

        $this->assertSame($viewModel, $response);
    }

    public function testGetNotFound()
    {
        $viewModel = new JsonModel();
        $application = new Application();

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('notFound')->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockQueryHandler = m::mock(QueryHandlerInterface::class);
        $mockQueryHandler->shouldReceive('handleQuery')
                         ->with($application)
                         ->andThrow(new Exception\NotFoundException());

        $mockLogger = $this->mockLogger();

        $mockSl = $this->getMockSl($mockResponse, $mockParams, $mockQueryHandler, 'QueryHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->get(25);

        $this->assertSame($viewModel, $response);
    }

    public function testGetClientError()
    {
        $viewModel = new JsonModel();
        $application = new Application();
        $errors = ['foo' => 'is not bar'];

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('error')->with(400, $errors)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockQueryHandler = m::mock(QueryHandlerInterface::class);
        $mockQueryHandler->shouldReceive('handleQuery')
            ->with($application)
            ->andThrow(new Exception\ValidationException($errors));

        $mockLogger = $this->mockLogger();

        $mockSl = $this->getMockSl($mockResponse, $mockParams, $mockQueryHandler, 'QueryHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->get(25);

        $this->assertSame($viewModel, $response);
    }

    public function testGetServerError()
    {
        $viewModel = new JsonModel();
        $application = new Application();
        $ex = new \Exception('blargle');
        $errors = ['blargle', explode('#', $ex->getTraceAsString())];

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('error')->with(500, $errors)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockQueryHandler = m::mock(QueryHandlerInterface::class);
        $mockQueryHandler->shouldReceive('handleQuery')
            ->with($application)
            ->andThrow($ex);

        $mockLogger = $this->mockLogger();

        $mockSl = $this->getMockSl($mockResponse, $mockParams, $mockQueryHandler, 'QueryHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->get(25);

        $this->assertSame($viewModel, $response);
    }

    public function testGetList()
    {
        $viewModel = new JsonModel();
        $application = new Application();
        $data = ['foo' => 'var'];
        $extra = ['bar' => 'cake'];
        $count = 54;

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('multipleResults')->with($count, $data, $extra)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockQueryHandler = m::mock(QueryHandlerInterface::class);
        $mockQueryHandler->shouldReceive('handleQuery')
                         ->with($application)
                         ->andReturn(['result'=>$data, 'count'=>$count, 'bar' => 'cake']);

        $mockLogger = $this->mockLogger();

        $mockSl = $this->getMockSl($mockResponse, $mockParams, $mockQueryHandler, 'QueryHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->getList();

        $this->assertSame($viewModel, $response);
    }

    public function testGetListNotFound()
    {
        $viewModel = new JsonModel();
        $application = new Application();

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('notFound')->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockQueryHandler = m::mock(QueryHandlerInterface::class);
        $mockQueryHandler->shouldReceive('handleQuery')
            ->with($application)
            ->andThrow(new Exception\NotFoundException());

        $mockLogger = $this->mockLogger();

        $mockSl = $this->getMockSl($mockResponse, $mockParams, $mockQueryHandler, 'QueryHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->getList();

        $this->assertSame($viewModel, $response);
    }

    public function testGetListClientError()
    {
        $viewModel = new JsonModel();
        $application = new Application();
        $errors = ['foo' => 'is not bar'];

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('error')->with(400, $errors)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockQueryHandler = m::mock(QueryHandlerInterface::class);
        $mockQueryHandler->shouldReceive('handleQuery')
            ->with($application)
            ->andThrow(new Exception\ValidationException($errors));

        $mockLogger = $this->mockLogger();

        $mockSl = $this->getMockSl($mockResponse, $mockParams, $mockQueryHandler, 'QueryHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->getList();

        $this->assertSame($viewModel, $response);
    }

    public function testGetListServerError()
    {
        $viewModel = new JsonModel();
        $application = new Application();
        $ex = new \Exception('blargle');
        $errors = ['blargle', explode('#', $ex->getTraceAsString())];

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('error')->with(500, $errors)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockQueryHandler = m::mock(QueryHandlerInterface::class);
        $mockQueryHandler->shouldReceive('handleQuery')
            ->with($application)
            ->andThrow($ex);

        $mockLogger = $this->mockLogger();

        $mockSl = $this->getMockSl($mockResponse, $mockParams, $mockQueryHandler, 'QueryHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->getList();

        $this->assertSame($viewModel, $response);
    }

    public function testUpdate()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();
        $result = new Result();

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('successfulUpdate')->with($result)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andReturn($result);

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->update(25, []);

        $this->assertSame($viewModel, $response);
    }

    public function testUpdateNotFound()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('notFound')->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andThrow(new Exception\NotFoundException());

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->update(25, []);

        $this->assertSame($viewModel, $response);
    }

    public function testUpdateClientError()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();
        $errors = ['foo' => 'is not bar'];

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('error')->with(400, $errors)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andThrow(new Exception\ValidationException($errors));

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->update(25, []);

        $this->assertSame($viewModel, $response);
    }

    public function testUpdateServerError()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();
        $ex = new \Exception('blargle');
        $errors = ['blargle', explode('#', $ex->getTraceAsString())];

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('error')->with(500, $errors)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andThrow($ex);

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->update(25, []);

        $this->assertSame($viewModel, $response);
    }


    public function testReplaceList()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();
        $result = new Result();

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('successfulUpdate')->with($result)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andReturn($result);

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->replaceList(25, []);

        $this->assertSame($viewModel, $response);
    }

    public function testReplaceListNotFound()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('notFound')->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andThrow(new Exception\NotFoundException());

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->replaceList(25, []);

        $this->assertSame($viewModel, $response);
    }

    public function testReplaceListClientError()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();
        $errors = ['foo' => 'is not bar'];

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('error')->with(400, $errors)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andThrow(new Exception\ValidationException($errors));

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->replaceList(25, []);

        $this->assertSame($viewModel, $response);
    }

    public function testReplaceListServerError()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();
        $ex = new \Exception('blargle');
        $errors = ['blargle', explode('#', $ex->getTraceAsString())];

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('error')->with(500, $errors)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andThrow($ex);

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->replaceList(25, []);

        $this->assertSame($viewModel, $response);
    }

    public function testCreate()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();
        $result = new Result();

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('successfulCreate')->with($result)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andReturn($result);

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->create([]);

        $this->assertSame($viewModel, $response);
    }

    public function testCreateClientError()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();
        $errors = ['foo' => 'is not bar'];

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('error')->with(400, $errors)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andThrow(new Exception\ValidationException($errors));

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->create([]);

        $this->assertSame($viewModel, $response);
    }

    public function testCreateServerError()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();
        $ex = new \Exception('blargle');
        $errors = ['blargle', explode('#', $ex->getTraceAsString())];

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('error')->with(500, $errors)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andThrow($ex);

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->create([]);

        $this->assertSame($viewModel, $response);
    }

    public function testDelete()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();
        $result = new Result();

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('successfulUpdate')->with($result)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andReturn($result);

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->delete(25);

        $this->assertSame($viewModel, $response);
    }

    public function testDeleteNotFound()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('notFound')->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andThrow(new Exception\NotFoundException());

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->delete(25);

        $this->assertSame($viewModel, $response);
    }

    public function testDeleteClientError()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();
        $errors = ['foo' => 'is not bar'];

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('error')->with(400, $errors)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andThrow(new Exception\ValidationException($errors));

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->delete(25);

        $this->assertSame($viewModel, $response);
    }

    public function testDeleteServerError()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();
        $ex = new \Exception('blargle');
        $errors = ['blargle', explode('#', $ex->getTraceAsString())];

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('error')->with(500, $errors)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andThrow($ex);

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->delete(25);

        $this->assertSame($viewModel, $response);
    }


    public function testDeleteList()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();
        $result = new Result();

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('successfulUpdate')->with($result)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andReturn($result);

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->deleteList();

        $this->assertSame($viewModel, $response);
    }

    public function testDeleteListNotFound()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('notFound')->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andThrow(new Exception\NotFoundException());

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->deleteList();

        $this->assertSame($viewModel, $response);
    }

    public function testDeleteListClientError()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();
        $errors = ['foo' => 'is not bar'];

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('error')->with(400, $errors)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andThrow(new Exception\ValidationException($errors));

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->deleteList();

        $this->assertSame($viewModel, $response);
    }

    public function testDeleteListServerError()
    {
        $viewModel = new JsonModel();
        $application = new UpdateTypeOfLicence();
        $ex = new \Exception('blargle');
        $errors = ['blargle', explode('#', $ex->getTraceAsString())];

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('error')->with(500, $errors)->andReturn($viewModel);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($application);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($application)
            ->andThrow($ex);

        $mockLogger = $this->mockLogger();

        $mockSl =
            $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler, 'CommandHandlerManager', $mockLogger);

        $sut = $this->setupSut($mockSl);

        $response = $sut->deleteList();

        $this->assertSame($viewModel, $response);
    }

    /**
     * @param $mockSl
     * @return GenericController
     */
    protected function setupSut($mockSl)
    {
        $sut = new GenericController();
        $sut->setPluginManager($mockSl);
        $sut->setServiceLocator($mockSl);
        return $sut;
    }

    /**
     * @param $mockResponse
     * @param $mockParams
     * @param $mockQueryHandler
     * @return m\MockInterface
     */
    protected function getMockSl(
        $mockResponse,
        $mockParams,
        $mockQueryHandler,
        $handlerType = 'QueryHandlerManager',
        $mockLogger = null
    ) {
        $mockSl = m::mock(PluginManager::class);
        $mockSl->shouldReceive('get')->with('response', null)->andReturn($mockResponse);
        $mockSl->shouldReceive('get')->with('params', null)->andReturn($mockParams);
        $mockSl->shouldReceive('get')->with($handlerType)->andReturn($mockQueryHandler);
        $mockSl->shouldReceive('get')->with('Logger')->andReturn($mockLogger);
        $mockSl->shouldReceive('setController');
        return $mockSl;
    }
}
