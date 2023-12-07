<?php

namespace Dvsa\OlcsTest\Cli\Controller;

use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Cli\Controller\UtilController;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

use Laminas\Mvc\MvcEvent;
use Laminas\Router\RouteMatch;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;

class UtilControllerTest extends MockeryTestCase
{
    /** @var UtilController */
    protected $sut;
    protected $sm;
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $event;
    protected $console;
    protected $mockQueryHandlerManager;

    public function setUp(): void
    {
        $this->request = m::mock('Laminas\Console\Request');

        $this->routeMatch = new RouteMatch([]);
        $this->event = new MvcEvent();
        $this->event->setRouteMatch($this->routeMatch);
        $sm = m::mock(ServiceManager::class);

        $sm->shouldReceive('setService')
            ->andReturnUsing(
                function ($alias, $service) use ($sm) {
                    $sm->shouldReceive('get')->with($alias)->andReturn($service);
                    $sm->shouldReceive('has')->with($alias)->andReturn(true);
                    return $sm;
                }
            );

        $this->sm = $sm;
        $this->console = m::mock('Laminas\Console\Adapter\AdapterInterface');
        $this->mockQueryHandlerManager = m::mock(QueryHandlerManager::class);

        $this->sut = m::mock(new UtilController($this->mockQueryHandlerManager))->makePartial();
        $this->sut->setEvent($this->event);
        $this->sut->setConsole($this->console);
    }

    public function testIndexAction()
    {
        /** @var Licence $licence */
        $licence = m::mock(Licence::class);
        /** @var RefData $refData */
        $statusId = 'apsts_not_submitted';
        $isVariation = "1";
        $refData = m::mock(new RefData($statusId))->makePartial();
        $application = new Application($licence, $refData, $isVariation);

        $result = new Result($application);

        $this->mockQueryHandlerManager->shouldReceive('handleQuery')->andReturn($result);

        $parameters = [
            'entityName' => 'Application\Application',
            'propertyName' => 'isVariation',
            'filterProperty' => 'id',
            'filterValue' => '1'
        ];

        $this->routeMatch->setParam('entity-name', $parameters['entityName']);
        $this->routeMatch->setParam('property-name', $parameters['propertyName']);
        $this->routeMatch->setParam('filter-property', $parameters['filterProperty']);
        $this->routeMatch->setParam('filter-value', $parameters['filterValue']);
        $expected = json_encode(["value" => $isVariation]) . PHP_EOL . '*** END OF OUTPUT ***' . PHP_EOL;
        $this->console->shouldReceive('writeLine');
        $this->assertSame($expected, $this->sut->getDbValueAction()->getResult());
        $this->assertSame(0, $this->sut->getDbValueAction()->getErrorLevel());
    }

    public function testIndexActionWithObject()
    {
        /** @var Licence $licence */
        $licence = m::mock(Licence::class);
        /** @var RefData $refData */
        $statusId = 'apsts_not_submitted';
        $isVariation = '1';
        $refData = m::mock(new RefData($statusId))->makePartial();
        $application = new Application($licence, $refData, $isVariation);
        $this->console->shouldReceive('writeLine');
        $result = new Result($application);

        $this->mockQueryHandlerManager->shouldReceive('handleQuery')->andReturn($result);

        $parameters = [
            'entityName' => 'Application\Application',
            'propertyName' => 'status',
            'filterProperty' => 'id',
            'filterValue' => '1'
        ];

        $this->routeMatch->setParam('entity-name', $parameters['entityName']);
        $this->routeMatch->setParam('property-name', $parameters['propertyName']);
        $this->routeMatch->setParam('filter-property', $parameters['filterProperty']);
        $this->routeMatch->setParam('filter-value', $parameters['filterValue']);
        $expected = json_encode(["value" => $statusId]) . PHP_EOL . '*** END OF OUTPUT ***' . PHP_EOL;
        $this->assertSame($expected, $this->sut->getDbValueAction()->getResult());
        $this->assertSame(0, $this->sut->getDbValueAction()->getErrorLevel());
    }

    /**
    * @dataProvider exceptionProvider
    */
    public function testIndexActionWithException($exception, $message)
    {
        $this->mockQueryHandlerManager->shouldReceive('handleQuery')->andThrow($exception, $message);

        $parameters = [
            'entityName' => 'Application\Application',
            'propertyName' => 'isVariation',
            'filterProperty' => 'id',
            'filterValue' => '1'
        ];

        $this->routeMatch->setParam('entity-name', $parameters['entityName']);
        $this->routeMatch->setParam('property-name', $parameters['propertyName']);
        $this->routeMatch->setParam('filter-property', $parameters['filterProperty']);
        $this->routeMatch->setParam('filter-value', $parameters['filterValue']);
        $this->console->shouldReceive('writeLine');
        $expected = json_encode(["error" => $message]) . PHP_EOL . '*** END OF OUTPUT ***' . PHP_EOL;
        $this->assertSame($expected, $this->sut->getDbValueAction()->getResult());
        $this->assertSame(1, $this->sut->getDbValueAction()->getErrorLevel());
    }

    public function exceptionProvider()
    {
        return [
            [\Dvsa\Olcs\Api\Domain\Exception\NotFoundException::class, 'Not Found Message'],
            [\Dvsa\Olcs\Api\Domain\Exception\Exception::class, 'Domain Exception Message'],
            [\Exception::class, 'Generic Exception Message']
        ];
    }

    public function testIndexActionWithVerboseAndException()
    {
        $this->mockQueryHandlerManager->shouldReceive('handleQuery')->andThrow(\Exception::class, 'Exception Message');

        $parameters = [
            'entityName' => 'Application\Application',
            'propertyName' => 'isVariation',
            'filterProperty' => 'id',
            'filterValue' => '1'
        ];

        $this->routeMatch->setParam('verbose', true);
        $this->routeMatch->setParam('entity-name', $parameters['entityName']);
        $this->routeMatch->setParam('property-name', $parameters['propertyName']);
        $this->routeMatch->setParam('filter-property', $parameters['filterProperty']);
        $this->routeMatch->setParam('filter-value', $parameters['filterValue']);

        $this->console->shouldReceive('writeLine');
        $this->console->shouldReceive('writeLine')->with('*** OUTPUT ***');
        $expected = json_encode(["error" => "Exception Message"]) . PHP_EOL . '*** END OF OUTPUT ***' . PHP_EOL;
        $this->assertSame($expected, $this->sut->getDbValueAction()->getResult());
        $this->assertSame(1, $this->sut->getDbValueAction()->getErrorLevel());
    }
}
