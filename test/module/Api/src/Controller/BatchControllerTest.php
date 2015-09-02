<?php

/**
 * Batch Controller Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Controller;

use Dvsa\Olcs\Transfer\Command\Application\UpdateTypeOfLicence;
use Dvsa\Olcs\Transfer\Query\Application\Application;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Zend\Mvc\Controller\Plugin\Params;
use Zend\Mvc\Controller\PluginManager;
use Zend\View\Model\JsonModel;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\Command;
use Dvsa\Olcs\Cli\Controller\BatchController;
use Zend\ServiceManager\ServiceManager;
use Zend\Console\Adapter\AdapterInterface;

/**
 * Batch Controller Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class BatchControllerTest extends TestCase
{
    protected $sut;

    protected $sm;

    protected $pm;

    protected function setUp()
    {
        $this->sut = new BatchController();

        $this->sm = m::mock(ServiceManager::class);
        $this->sut->setServiceLocator($this->sm);

        $this->pm = m::mock(PluginManager::class);
        $this->pm->shouldReceive('setController');

        $this->sut->setPluginManager($this->pm);

    }

    public function testLicenceStatusRulesActionVerboseMessages()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(true);

        $mockConsole = m::mock(AdapterInterface::class);

        $mockConsole->shouldReceive('writeLine');

        $this->sut->setConsole($mockConsole);

        $this->sut->licenceStatusRulesAction();
    }

    public function testLicenceStatusRules()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $mockCommandHandler = m::mock();
        $this->sm->shouldReceive('get')->with('CommandHandlerManager')->andReturn($mockCommandHandler);

        $mockCommandHandler->shouldReceive('handleCommand')->twice()->andReturn(new Command\Result());

        $this->sut->licenceStatusRulesAction();
    }

    public function testLicenceStatusRulesNotFound()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $mockCommandHandler = m::mock();
        $this->sm->shouldReceive('get')->with('CommandHandlerManager')->andReturn($mockCommandHandler);

        $mockCommandHandler->shouldReceive('handleCommand')->once()->andThrow(Exception\NotFoundException::class);

        /* @var $result \Zend\View\Model\ConsoleModel */
        $result = $this->sut->licenceStatusRulesAction();

        $this->assertSame(404, $result->getErrorLevel());
    }

    public function testLicenceStatusRulesDomainException()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $mockCommandHandler = m::mock();
        $this->sm->shouldReceive('get')->with('CommandHandlerManager')->andReturn($mockCommandHandler);

        $mockCommandHandler->shouldReceive('handleCommand')->once()->andThrow(Exception\RuntimeException::class);

        /* @var $result \Zend\View\Model\ConsoleModel */
        $result = $this->sut->licenceStatusRulesAction();

        $this->assertSame(400, $result->getErrorLevel());
    }

    public function testLicenceStatusRulesException()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $mockCommandHandler = m::mock();
        $this->sm->shouldReceive('get')->with('CommandHandlerManager')->andReturn($mockCommandHandler);

        $mockCommandHandler->shouldReceive('handleCommand')->once()->andThrow(\Exception::class);

        /* @var $result \Zend\View\Model\ConsoleModel */
        $result = $this->sut->licenceStatusRulesAction();

        $this->assertSame(500, $result->getErrorLevel());
    }

    public function testEnqueueCompaniesHouseCompareAction()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $mockCommandHandler = m::mock();
        $this->sm->shouldReceive('get')->with('CommandHandlerManager')->andReturn($mockCommandHandler);

        $mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\CompaniesHouse\EnqueueOrganisations::class))
            ->once()
            ->andReturn(new Command\Result());

        $this->sut->enqueueCompaniesHouseCompareAction();
    }

    public function testDuplicateVehicleWarningAction()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $mockCommandHandler = m::mock();
        $this->sm->shouldReceive('get')->with('CommandHandlerManager')->andReturn($mockCommandHandler);

        $mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\Vehicle\ProcessDuplicateVehicleWarnings::class))
            ->once()
            ->andReturn(new Command\Result());

        $this->sut->duplicateVehicleWarningAction();
    }
}
