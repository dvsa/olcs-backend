<?php


namespace Dvsa\OlcsTest\Api\Controller;

use Dvsa\Olcs\Api\Controller\XmlController;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\CommandHandlerInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Laminas\Http\Response as HttpResponse;
use Dvsa\Olcs\Api\Mvc\Controller\Plugin\Response;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Laminas\Mvc\Controller\Plugin\Params;
use Laminas\Mvc\Controller\PluginManager;
use Dvsa\Olcs\Api\Domain\Exception;

/**
 * Class XmlControllerTest
 */
class XmlControllerTest extends TestCase
{
    /**
     * @param $mockSl
     * @return XmlController
     */
    protected function setupSut($mockSl)
    {
        $sut = new XmlController();
        $sut->setPluginManager($mockSl);
        $sut->setServiceLocator($mockSl);
        return $sut;
    }

    /**
     * Tests create when a valid response is returned
     */
    public function testCreate()
    {
        $validResponse = new HttpResponse();
        $validResponse->setStatusCode(HttpResponse::STATUS_CODE_202);
        $command = m::mock(CommandInterface::class);
        $result = new Result();

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('xmlAccepted')->andReturn($validResponse);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($command);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')->with($command)->andReturn($result);

        $mockSl = $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler);

        $sut = $this->setupSut($mockSl);

        $controllerResponse = $sut->create([]);

        $this->assertSame($validResponse, $controllerResponse);
    }

    /**
     * Tests create when an exception is thrown
     */
    public function testCreateWithException()
    {
        $badRequestResponse = new HttpResponse();
        $badRequestResponse->setStatusCode(HttpResponse::STATUS_CODE_400);
        $command = m::mock(CommandInterface::class);

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('xmlBadRequest')->andReturn($badRequestResponse);

        $mockParams = m::mock(Params::class);
        $mockParams->shouldReceive('__invoke')->with('dto')->andReturn($command);

        $mockCommandHandler = m::mock(CommandHandlerInterface::class);
        $mockCommandHandler->shouldReceive('handleCommand')
            ->with($command)
            ->andThrow('\Dvsa\Olcs\Api\Domain\Exception\Exception');

        $mockSl = $this->getMockSl($mockResponse, $mockParams, $mockCommandHandler);

        $sut = $this->setupSut($mockSl);

        $controllerResponse = $sut->create([]);

        $this->assertSame($badRequestResponse, $controllerResponse);
    }


    /**
     * @param $mockResponse
     * @param $mockParams
     * @param $mockCommandHandler
     * @return m\MockInterface
     */
    protected function getMockSl($mockResponse, $mockParams, $mockCommandHandler)
    {
        $mockSl = m::mock(PluginManager::class);
        $mockSl->shouldReceive('get')->with('response', null)->andReturn($mockResponse);
        $mockSl->shouldReceive('get')->with('params', null)->andReturn($mockParams);
        $mockSl->shouldReceive('get')->with('CommandHandlerManager')->andReturn($mockCommandHandler);
        $mockSl->shouldReceive('setController');

        return $mockSl;
    }
}
