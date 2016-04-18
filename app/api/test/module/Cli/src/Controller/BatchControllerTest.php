<?php

/**
 * Batch Controller Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Controller;

use Dvsa\Olcs\Api\Domain\Command;
use Dvsa\Olcs\Cli\Domain\Command as CliCommand;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\Query;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Cli\Controller\BatchController;
use Dvsa\Olcs\Transfer\Command\Application\UpdateTypeOfLicence;
use Dvsa\Olcs\Transfer\Query\Application\Application;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Zend\Console\Adapter\AdapterInterface;
use Zend\Mvc\Controller\Plugin\Params;
use Zend\Mvc\Controller\PluginManager;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\JsonModel;

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

    public function testContinuationNotSoughtAction()
    {
        $mockParams =  m::mock(\Zend\Mvc\Controller\Plugin\Params::class)
            ->shouldReceive('__invoke')
            ->andReturnUsing(
                function ($param) {
                    $map = [
                        'dryrun' => false,
                        'verbose' => true,
                    ];
                    return isset($map[$param]) && $map[$param];
                }
            )
            ->getMock();

        $this->pm
            ->shouldReceive('get')
            ->with('params', null)
            ->andReturn($mockParams);

        $mockCommandHandler = m::mock();
        $mockQueryHandler = m::mock();
        $mockConsole = m::mock(AdapterInterface::class);

        $now = new DateTime();

        $this->sm
            ->shouldReceive('get')
            ->with('CommandHandlerManager')
            ->andReturn($mockCommandHandler)
            ->shouldReceive('get')
            ->with('QueryHandlerManager')
            ->andReturn($mockQueryHandler);

        $licences = [
            [
                'id' => 1,
                'version' => 1,
                'licNo' => 'OB001',
                'trafficArea' => [
                    'id' => 'B',
                    'name' => 'North East',
                ],
            ],
            [
                'id' => 2,
                'version' => 1,
                'licNo' => 'OB002',
                'trafficArea' => [
                    'id' => 'B',
                    'name' => 'North East',
                ],
            ],
        ];

        $mockQueryHandler
            ->shouldReceive('handleQuery')
            ->with(m::type(Query\Licence\ContinuationNotSoughtList::class))
            ->andReturnUsing(
                function ($qry) use ($licences, $now) {
                    $this->assertEquals(
                        $now->format('Y-m-d'),
                        $qry->getDate()->format('Y-m-d')
                    );
                    return [
                        'result' => $licences,
                        'count' => 2,
                    ];
                }
            );

        $mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\Licence\ProcessContinuationNotSought::class))
            ->twice()
            ->andReturnUsing(
                function ($cmd) {
                    $result = new Command\Result();
                    $result->addMessage('Licence updated');
                    return $result;
                }
            );

        $mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\Email\SendContinuationNotSought::class))
            ->once()
            ->andReturnUsing(
                function ($cmd) use ($licences, $now) {
                    $this->assertEquals($now->format('Y-m-d'), $cmd->getDate()->format('Y-m-d'));
                    $this->assertSame($licences, $cmd->getLicences());
                    $result = new Command\Result();
                    $result->addMessage('Email sent');
                    return $result;
                }
            );

        $mockConsole = m::mock(AdapterInterface::class);
        $mockConsole->shouldReceive('writeLine')->times(9);
        $this->sut->setConsole($mockConsole);

        $this->sut->continuationNotSoughtAction();
    }

    public function testSetSystemParameter()
    {
        $mockConsole = m::mock(AdapterInterface::class);
        $mockConsole->shouldReceive('writeLine');
        $this->sut->setConsole($mockConsole);

        $this->pm->shouldReceive('get')->with('params', null)->andReturn('NAME', 'VALUE');

        $mockCommandHandler = m::mock();
        $this->sm->shouldReceive('get')->with('CommandHandlerManager')->andReturn($mockCommandHandler);

        $mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\SystemParameter\Update::class))
            ->once()
            ->andReturnUsing(
                function ($dto) {
                    $this->assertSame('NAME', $dto->getId());
                    $this->assertSame('VALUE', $dto->getParamValue());

                    return new Command\Result();
                }
            );

        $response = $this->sut->setSystemParameterAction();
        $this->assertSame(0, $response->getErrorLevel());
    }

    public function testSetSystemParameterMissing()
    {
        $mockConsole = m::mock(AdapterInterface::class);
        $mockConsole->shouldReceive('writeLine');
        $this->sut->setConsole($mockConsole);

        $this->pm->shouldReceive('get')->with('params', null)->andReturn('MISSING', 'VALUE');

        $mockCommandHandler = m::mock();
        $this->sm->shouldReceive('get')->with('CommandHandlerManager')->andReturn($mockCommandHandler);

        $mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\SystemParameter\Update::class))
            ->once()
            ->andThrow(Exception\NotFoundException::class);

        $response = $this->sut->setSystemParameterAction();
        $this->assertSame(404, $response->getErrorLevel());
    }

    public function testCreateViExtractFilesAction()
    {
        $mockConsole = m::mock(AdapterInterface::class);
        $mockConsole->shouldReceive('writeLine');
        $this->sut->setConsole($mockConsole);

        $this->pm->shouldReceive('get')->with('params', null)->andReturn(true, true, true, true, true);

        $mockCommandHandler = m::mock();
        $this->sm->shouldReceive('get')->with('CommandHandlerManager')->andReturn($mockCommandHandler);

        $mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(CliCommand\SetViFlags::class))
            ->once()
            ->andReturn(new Command\Result());
        $mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(CliCommand\CreateViExtractFiles::class))
            ->once()
            ->andReturn(new Command\Result());

        $this->sut->createViExtractFilesAction();
    }
}
