<?php

namespace Dvsa\OlcsTest\Cli\Controller;

use Dvsa\Olcs\Api\Domain\Command;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\Query;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Cli\Controller\BatchController;
use Dvsa\Olcs\Cli\Domain\Command as CliCommand;
use Dvsa\Olcs\Transfer\Command\Application\NotTakenUpApplication;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Console\Adapter\AdapterInterface;
use Zend\Http\Response;
use Zend\Mvc\Controller\Plugin\Params;
use Zend\Mvc\Controller\PluginManager;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\JsonModel;

/**
 * Batch Controller Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class BatchControllerTest extends MockeryTestCase
{
    /** @var  BatchController */
    protected $sut;

    /** @var ServiceManager|m\MockInterface */
    private $sm;
    /** @var PluginManager|m\MockInterface */
    private $pm;
    /** @var  AdapterInterface|m\MockInterface */
    private $mockConsole;
    /** @var m\MockInterface */
    private $mockCommandHandler;

    protected function setUp()
    {
        $this->mockCommandHandler = m::mock(CommandHandlerManager::class);

        $this->sm = m::mock(ServiceManager::class)
            ->shouldReceive('get')->with('CommandHandlerManager')->andReturn($this->mockCommandHandler)
            ->getMock();

        $this->pm = m::mock(PluginManager::class);
        $this->pm->shouldReceive('setController');

        $this->mockConsole = m::mock(AdapterInterface::class);

        $this->sut = new BatchController();
        $this->sut
            ->setConsole($this->mockConsole)
            ->setPluginManager($this->pm)
            ->setServiceLocator($this->sm);
    }

    public function testLicenceStatusRulesActionVerboseMessages()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(true);
        $this->mockConsole->shouldReceive('writeLine');

        $this->sut->licenceStatusRulesAction();
    }

    public function testLicenceStatusRules()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->twice()
            ->andReturn(new Command\Result());

        $this->sut->licenceStatusRulesAction();
    }

    public function testLicenceStatusRulesNotFound()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->once()
            ->andThrow(Exception\NotFoundException::class);

        /* @var $result \Zend\View\Model\ConsoleModel */
        $result = $this->sut->licenceStatusRulesAction();

        static::assertSame(404, $result->getErrorLevel());
    }

    public function testLicenceStatusRulesDomainException()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->once()
            ->andThrow(Exception\RuntimeException::class);

        /* @var $result \Zend\View\Model\ConsoleModel */
        $result = $this->sut->licenceStatusRulesAction();

        static::assertSame(Response::STATUS_CODE_400, $result->getErrorLevel());
    }

    public function testLicenceStatusRulesException()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->once()
            ->andThrow(\Exception::class);

        /* @var $result \Zend\View\Model\ConsoleModel */
        $result = $this->sut->licenceStatusRulesAction();

        static::assertSame(500, $result->getErrorLevel());
    }

    public function testEnqueueCompaniesHouseCompareAction()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\CompaniesHouse\EnqueueOrganisations::class))
            ->once()
            ->andReturn(new Command\Result());

        $this->sut->enqueueCompaniesHouseCompareAction();
    }

    public function testDuplicateVehicleWarningAction()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\Vehicle\ProcessDuplicateVehicleWarnings::class))
            ->once()
            ->andReturn(new Command\Result());

        $this->sut->duplicateVehicleWarningAction();
    }

    public function testContinuationNotSoughtAction()
    {
        $this->mockParamsPlugin(
            [
                'dryrun' => false,
                'verbose' => true,
            ]
        );

        $mockQueryHandler = m::mock();

        $now = new DateTime();

        $this->sm
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
                function (Query\Licence\ContinuationNotSoughtList $qry) use ($licences, $now) {
                    static::assertEquals(
                        $now->format('Y-m-d'),
                        $qry->getDate()->format('Y-m-d')
                    );
                    return [
                        'result' => $licences,
                        'count' => 2,
                    ];
                }
            );

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\Licence\ProcessContinuationNotSought::class))
            ->twice()
            ->andReturn(
                (new Command\Result())
                    ->addMessage('Licence updated')
            );

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\Email\SendContinuationNotSought::class))
            ->once()
            ->andReturnUsing(
                function (Command\Email\SendContinuationNotSought $cmd) use ($licences, $now) {
                    static::assertEquals($now->format('Y-m-d'), $cmd->getDate()->format('Y-m-d'));
                    static::assertSame($licences, $cmd->getLicences());
                    $result = new Command\Result();
                    $result->addMessage('Email sent');
                    return $result;
                }
            );

        $this->mockConsole->shouldReceive('writeLine')->times(9);

        $this->sut->continuationNotSoughtAction();
    }

    public function testSetSystemParameter()
    {
        $this->mockConsole->shouldReceive('writeLine');

        $this->pm->shouldReceive('get')->with('params', null)->andReturn('NAME', 'VALUE');

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\SystemParameter\Update::class))
            ->once()
            ->andReturnUsing(
                function (Command\SystemParameter\Update $dto) {
                    static::assertSame('NAME', $dto->getId());
                    static::assertSame('VALUE', $dto->getParamValue());

                    return new Command\Result();
                }
            );

        $response = $this->sut->setSystemParameterAction();
        static::assertSame(0, $response->getErrorLevel());
    }

    public function testSetSystemParameterMissing()
    {
        $this->mockConsole->shouldReceive('writeLine');

        $this->pm->shouldReceive('get')->with('params', null)->andReturn('MISSING', 'VALUE');

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\SystemParameter\Update::class))
            ->once()
            ->andThrow(Exception\NotFoundException::class);

        $response = $this->sut->setSystemParameterAction();
        static::assertSame(404, $response->getErrorLevel());
    }

    public function testCreateViExtractFilesAction()
    {
        $this->mockConsole->shouldReceive('writeLine');

        $this->pm->shouldReceive('get')->with('params', null)->andReturn(true, true, true, true, true);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(CliCommand\SetViFlags::class))
            ->once()
            ->andReturn(new Command\Result());
        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(CliCommand\CreateViExtractFiles::class))
            ->once()
            ->andReturn(new Command\Result());

        $this->sut->createViExtractFilesAction();
    }

    public function testProcessNtuAction()
    {
        $this->mockParamsPlugin(
            [
                'dryrun' => false,
                'verbose' => true,
            ]
        );

        $mockQueryHandler = m::mock();

        $now = new DateTime();

        $this->sm
            ->shouldReceive('get')
            ->with('QueryHandlerManager')
            ->andReturn($mockQueryHandler);

        $application = [
            'id' => 1,
        ];

        $applications = [$application];

        $mockQueryHandler
            ->shouldReceive('handleQuery')
            ->with(m::type(Query\Application\NotTakenUpList::class))
            ->andReturnUsing(
                function (Query\Application\NotTakenUpList $qry) use ($applications, $now) {
                    static::assertEquals(
                        $now->format('Y-m-d'),
                        $qry->getDate()->format('Y-m-d')
                    );
                    return [
                        'result' => $applications,
                        'count' => 1,
                    ];
                }
            );

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(NotTakenUpApplication::class))
            ->once()
            ->andReturn(
                (new Command\Result())
                    ->addMessage('Processing Application ID 1')
            );

        $this->mockConsole->shouldReceive('writeLine')->times(5);

        $this->sut->processNtuAction();
    }

    /**
     * @dataProvider exceptionClassesProvider
     *
     * @param string $exceptionClass
     * @param int    $outputCount
     */
    public function testProcessNtuActionWithExceptions($exceptionClass, $outputCount)
    {
        $this->mockParamsPlugin(
            [
                'dryrun' => false,
                'verbose' => true,
            ]
        );

        $mockQueryHandler = m::mock()
            ->shouldReceive('handleQuery')
            ->with(m::type(Query\Application\NotTakenUpList::class))
            ->andThrow($exceptionClass)
            ->once()
            ->getMock();

        $this->sm
            ->shouldReceive('get')
            ->with('QueryHandlerManager')
            ->andReturn($mockQueryHandler);

        $this->mockConsole->shouldReceive('writeLine')->times($outputCount);

        $this->sut->processNtuAction();
    }

    /**
     * Exception classes provider
     */
    public function exceptionClassesProvider()
    {
        return [
            [Exception\NotFoundException::class, 3],
            [Exception\Exception::class, 2],
            [\Exception::class, 2],
        ];
    }

    public function testProcessNtuActionWithDryRun()
    {
        $this->mockParamsPlugin(
            [
                'dryrun' => true,
                'verbose' => true,
            ]
        );

        $mockQueryHandler = m::mock();

        $now = new DateTime();

        $this->sm
            ->shouldReceive('get')
            ->with('QueryHandlerManager')
            ->andReturn($mockQueryHandler);

        $application = ['id' => 1];

        $applications = [$application];

        $mockQueryHandler
            ->shouldReceive('handleQuery')
            ->with(m::type(Query\Application\NotTakenUpList::class))
            ->andReturnUsing(
                function (Query\Application\NotTakenUpList $qry) use ($applications, $now) {
                    static::assertEquals(
                        $now->format('Y-m-d'),
                        $qry->getDate()->format('Y-m-d')
                    );
                    return [
                        'result' => $applications,
                        'count' => 1,
                    ];
                }
            );

        $this->mockConsole->shouldReceive('writeLine')->times(3);

        $this->sut->processNtuAction();
    }

    public function testDataGovUkExport()
    {
        $this->mockParamsPlugin(
            [
                'report-name' => 'unit_ReportName',
                'path' => 'unit_Path',
                'verbose' => true,
            ]
        );

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(CliCommand\DataGovUkExport::class))
            ->once()
            ->andReturn(
                (new Command\Result())
                    ->addMessage('unit_message')
            );

        $this->mockConsole
            ->shouldReceive('writeLine')->once()->with('/' . addslashes(CliCommand\DataGovUkExport::class) . '$/')
            ->shouldReceive('writeLine')->once()->with('/unit_message$/');

        $this->sut->dataGovUkExportAction();
    }

    private function mockParamsPlugin(array $map)
    {
        $mockParams = m::mock(\Zend\Mvc\Controller\Plugin\Params::class)
            ->shouldReceive('__invoke')
            ->andReturnUsing(
                function ($param) use ($map) {
                    return isset($map[$param]) && $map[$param];
                }
            )
            ->getMock();

        $this->pm
            ->shouldReceive('get')
            ->with('params', null)
            ->andReturn($mockParams);
    }
}
