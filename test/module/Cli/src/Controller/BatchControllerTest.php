<?php

namespace Dvsa\OlcsTest\Cli\Controller;

use Dvsa\Olcs\Api\Domain\Command;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\Query;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Cli\Controller\BatchController;
use Dvsa\Olcs\Cli\Domain\Command as CliCommand;
use Dvsa\Olcs\Cli\Domain\Query as CliQuery;
use Dvsa\Olcs\Transfer\Command\Application\NotTakenUpApplication;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Console\Adapter\AdapterInterface;
use Zend\Http\Response;
use Zend\Mvc\Controller\PluginManager;
use Zend\ServiceManager\ServiceManager;

/**
 * @covers \Dvsa\Olcs\Cli\Controller\BatchController
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
    /** @var m\MockInterface */
    private $mockQueryHandler;

    protected function setUp()
    {
        $this->mockCommandHandler = m::mock(CommandHandlerManager::class);
        $this->mockQueryHandler = m::mock(QueryHandlerManager::class);

        $this->sm = m::mock(ServiceManager::class)
            ->shouldReceive('get')->with('CommandHandlerManager')->andReturn($this->mockCommandHandler)
            ->shouldReceive('get')->with('QueryHandlerManager')->andReturn($this->mockQueryHandler)
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

    public function testCompaniesHouseVsOlcsDiffsExport()
    {
        $this->mockParamsPlugin(
            [
                'path' => 'unit_Path',
                'verbose' => true,
            ]
        );

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(CliCommand\CompaniesHouseVsOlcsDiffsExport::class))
            ->once()
            ->andReturn(
                (new Command\Result())
                    ->addMessage('unit_message')
            );

        $this->mockConsole
            ->shouldReceive('writeLine')
            ->once()
            ->with(m::pattern('/' . addslashes(CliCommand\CompaniesHouseVsOlcsDiffsExport::class) . '$/'))
            ->shouldReceive('writeLine')->once()->with(m::pattern('/unit_message$/'));

        $this->sut->companiesHouseVsOlcsDiffsExportAction();
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

    public function testDuplicateVehicleRemovalAction()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\Vehicle\ProcessDuplicateVehicleRemoval::class))
            ->once()
            ->andReturn(new Command\Result());

        $this->sut->duplicateVehicleRemovalAction();
    }

    public function testContinuationNotSoughtAction()
    {
        $this->mockParamsPlugin(
            [
                'dryrun' => false,
                'verbose' => true,
            ]
        );

        $now = new DateTime();

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

        $this->mockQueryHandler
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
            ->with(m::type(Command\Licence\EnqueueContinuationNotSought::class))
            ->once()
            ->andReturnUsing(
                function (Command\Licence\EnqueueContinuationNotSought $cmd) use ($licences, $now) {
                    static::assertEquals($now->format('Y-m-d'), $cmd->getDate()->format('Y-m-d'));
                    static::assertSame($licences, $cmd->getLicences());
                    return (new Command\Result());
                }
            );

        $this->mockConsole->shouldReceive('writeLine')->times(3);

        $this->sut->continuationNotSoughtAction();
    }

    public function testContinuationNotSoughtActionNoLicences()
    {
        $this->mockParamsPlugin(
            [
                'dryrun' => false,
                'verbose' => true,
            ]
        );

        $now = new DateTime();

        $licences = [];

        $this->mockQueryHandler
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
                        'count' => 0,
                    ];
                }
            );

        $this->mockConsole->shouldReceive('writeLine')->times(2);

        $this->sut->continuationNotSoughtAction();
    }

    public function testContinuationNotSoughtActionDryRun()
    {
        $this->mockParamsPlugin(
            [
                'dryrun' => true,
                'verbose' => true,
            ]
        );

        $now = new DateTime();

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

        $this->mockQueryHandler
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
            ->with(m::type(Command\Licence\EnqueueContinuationNotSought::class))
            ->never();

        $this->mockConsole->shouldReceive('writeLine')->times(2);

        $this->sut->continuationNotSoughtAction();
    }

    public function testCreatePsvLicenceSurrenderTasksActionDryRun()
    {
        $this->mockParamsPlugin(
            [
                'dryrun' => true,
                'verbose' => true,
            ]
        );

        $now = new DateTime();

        $licenceIds = [1, 2];

        $this->mockQueryHandler
            ->shouldReceive('handleQuery')
            ->with(m::type(Query\Licence\PsvLicenceSurrenderList::class))
            ->andReturnUsing(
                function (Query\Licence\PsvLicenceSurrenderList $qry) use ($licenceIds, $now) {
                    $this->assertEquals(
                        $now->format('Y-m-d'),
                        $qry->getDate()->format('Y-m-d')
                    );
                    return [
                        'result' => $licenceIds,
                        'count' => 2,
                    ];
                }
            );

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\Licence\CreateSurrenderPsvLicenceTasks::class))
            ->never();

        $this->mockConsole->shouldReceive('writeLine')->times(2);

        $this->sut->createPsvLicenceSurrenderTasksAction();
    }

    public function testCreatePsvLicenceSurrenderTasksActionNoLicences()
    {
        $this->mockParamsPlugin(
            [
                'dryrun' => false,
                'verbose' => true,
            ]
        );

        $now = new DateTime();

        $licenceIds = [];

        $this->mockQueryHandler
            ->shouldReceive('handleQuery')
            ->with(m::type(Query\Licence\PsvLicenceSurrenderList::class))
            ->andReturnUsing(
                function (Query\Licence\PsvLicenceSurrenderList $qry) use ($licenceIds, $now) {
                    $this->assertEquals(
                        $now->format('Y-m-d'),
                        $qry->getDate()->format('Y-m-d')
                    );
                    return [
                        'result' => $licenceIds,
                        'count' => 0,
                    ];
                }
            );

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\Licence\CreateSurrenderPsvLicenceTasks::class))
            ->never();

        $this->mockConsole->shouldReceive('writeLine')->times(2);

        $this->sut->createPsvLicenceSurrenderTasksAction();
    }

    public function testCreatePsvLicenceSurrenderTasksAction()
    {
        $this->mockParamsPlugin(
            [
                'dryrun' => false,
                'verbose' => true,
            ]
        );

        $now = new DateTime();

        $licenceIds = [1, 2];

        $this->mockQueryHandler
            ->shouldReceive('handleQuery')
            ->with(m::type(Query\Licence\PsvLicenceSurrenderList::class))
            ->andReturnUsing(
                function (Query\Licence\PsvLicenceSurrenderList $qry) use ($licenceIds, $now) {
                    $this->assertEquals(
                        $now->format('Y-m-d'),
                        $qry->getDate()->format('Y-m-d')
                    );
                    return [
                        'result' => $licenceIds,
                        'count' => 0,
                    ];
                }
            );

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\Licence\CreateSurrenderPsvLicenceTasks::class))
            ->once()
            ->andReturnUsing(
                function (Command\Licence\CreateSurrenderPsvLicenceTasks $cmd) use ($licenceIds) {
                    $this->assertSame($licenceIds, $cmd->getIds());
                    return (new Command\Result());
                }
            );

        $this->mockConsole->shouldReceive('writeLine')->times(3);

        $this->sut->createPsvLicenceSurrenderTasksAction();
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

        $now = new DateTime();

        $application = [
            'id' => 1,
        ];

        $applications = [$application];

        $this->mockQueryHandler
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

        $this->mockQueryHandler
            ->shouldReceive('handleQuery')
            ->with(m::type(Query\Application\NotTakenUpList::class))
            ->andThrow($exceptionClass)
            ->once()
            ->getMock();

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

        $now = new DateTime();

        $application = ['id' => 1];

        $applications = [$application];

        $this->mockQueryHandler
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

        $this->mockConsole->shouldReceive('writeLine')->times(2);

        $this->sut->dataGovUkExportAction();
    }

    public function testDataDvaNiExport()
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
            ->with(m::type(CliCommand\DataDvaNiExport::class))
            ->once()
            ->andReturn(
                (new Command\Result())
                    ->addMessage('unit_message')
            );

        $this->mockConsole
            ->shouldReceive('writeLine')->times(2);

        $this->sut->dataDvaNiExportAction();
    }

    public function testProcessCommunityLicencesAction()
    {
        $this->mockParamsPlugin(
            [
                'dryrun' => false,
                'verbose' => true,
            ]
        );

        $now = new DateTime();

        $communityLicencesToActivate = [
            ['id' => 1],
            ['id' => 2]
        ];
        $communityLicencesToSuspend = [
            ['id' => 3],
            ['id' => 4]
        ];

        $this->mockQueryHandler
            ->shouldReceive('handleQuery')
            ->with(m::type(CliQuery\CommunityLic\CommunityLicencesForActivationList::class))
            ->andReturnUsing(
                function (CliQuery\CommunityLic\CommunityLicencesForActivationList $qry) use (
                    $communityLicencesToActivate,
                    $now
                ) {
                    $this->assertEquals(
                        $now->format('Y-m-d'),
                        $qry->getDate()->format('Y-m-d')
                    );
                    return [
                        'result' => $communityLicencesToActivate,
                        'count' => 2,
                    ];
                }
            )
            ->once()
            ->shouldReceive('handleQuery')
            ->with(m::type(CliQuery\CommunityLic\CommunityLicencesForSuspensionList::class))
            ->andReturnUsing(
                function (CliQuery\CommunityLic\CommunityLicencesForSuspensionList $qry) use (
                    $communityLicencesToSuspend,
                    $now
                ) {
                    $this->assertEquals(
                        $now->format('Y-m-d'),
                        $qry->getDate()->format('Y-m-d')
                    );
                    return [
                        'result' => $communityLicencesToSuspend,
                        'count' => 2,
                    ];
                }
            )
            ->once();

        $idsForActivation = [1, 2];
        $idsForSuspension = [3, 4];
        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(CliCommand\CommunityLic\Activate::class))
            ->andReturnUsing(
                function (CliCommand\CommunityLic\Activate $cmd) use ($idsForActivation) {
                    $this->assertSame($idsForActivation, $cmd->getCommunityLicenceIds());
                    $result = new Command\Result();
                    $result->addMessage('Community licence 1 activated');
                    $result->addMessage('Community licence 2 activated');
                    return $result;
                }
            )
            ->once()
            ->shouldReceive('handleCommand')
            ->with(m::type(CliCommand\CommunityLic\Suspend::class))
            ->andReturnUsing(
                function (CliCommand\CommunityLic\Suspend $cmd) use ($idsForSuspension) {
                    $this->assertSame($idsForSuspension, $cmd->getCommunityLicenceIds());
                    $result = new Command\Result();
                    $result->addMessage('Community licence 3 suspended');
                    $result->addMessage('Community licence 4 suspended');
                    return $result;
                }
            )
            ->once();

        $this->mockConsole->shouldReceive('writeLine')->times(10);

        $this->sut->processCommunityLicencesAction();
    }

    public function testProcessCommunityLicencesActionNoLicences()
    {
        $this->mockParamsPlugin(
            [
                'dryrun' => false,
                'verbose' => true,
            ]
        );

        $now = new DateTime();

        $this->mockQueryHandler
            ->shouldReceive('handleQuery')
            ->with(m::type(CliQuery\CommunityLic\CommunityLicencesForActivationList::class))
            ->andReturnUsing(
                function (CliQuery\CommunityLic\CommunityLicencesForActivationList $qry) use ($now) {
                    $this->assertEquals(
                        $now->format('Y-m-d'),
                        $qry->getDate()->format('Y-m-d')
                    );
                    return [
                        'result' => [],
                        'count' => 0,
                    ];
                }
            )
            ->once()
            ->shouldReceive('handleQuery')
            ->with(m::type(CliQuery\CommunityLic\CommunityLicencesForSuspensionList::class))
            ->andReturnUsing(
                function (CliQuery\CommunityLic\CommunityLicencesForSuspensionList $qry) use ($now) {
                    $this->assertEquals(
                        $now->format('Y-m-d'),
                        $qry->getDate()->format('Y-m-d')
                    );
                    return [
                        'result' => [],
                        'count' => 0,
                    ];
                }
            )
            ->once();

        $this->mockConsole->shouldReceive('writeLine')->times(4);

        $this->sut->processCommunityLicencesAction();
    }

    public function testImportUserFromCsv()
    {
        $this->mockParamsPlugin(
            [
                'csv-path' => 'unit_source-csv-file',
                'result-csv-path' => 'unit_result-csv-file',
                'verbose' => true,
            ]
        );

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(CliCommand\ImportUsersFromCsv::class))
            ->once()
            ->andReturn(
                (new Command\Result())
                    ->addMessage('unit_message')
            );

        $this->mockConsole
            ->shouldReceive('writeLine')->times(2);

        $this->sut->importUsersFromCsvAction();
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

    public function testRemoveReadAuditAction()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(\Dvsa\Olcs\Cli\Domain\Command\RemoveReadAudit::class))
            ->once()
            ->andReturn(new Command\Result());

        $this->sut->removeReadAuditAction();
    }

    public function testInspectionRequestEmailAction()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(\Dvsa\Olcs\Email\Domain\Command\ProcessInspectionRequestEmail::class))
            ->once()
            ->andReturn(new Command\Result());

        $this->sut->inspectionRequestEmailAction();
    }

    public function testProcessInboxDocumentsAction()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\Correspondence\ProcessInboxDocuments::class))
            ->once()
            ->andReturn(new Command\Result());

        $this->sut->processInboxDocumentsAction();
    }

    public function testResolvePaymentsAction()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\Transaction\ResolveOutstandingPayments::class))
            ->once()
            ->andReturn(new Command\Result());

        $this->sut->resolvePaymentsAction();
    }

    public function testFlagUrgentTasksAction()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(\Dvsa\Olcs\Transfer\Command\Task\FlagUrgentTasks::class))
            ->once()
            ->andReturn(new Command\Result());

        $this->sut->flagUrgentTasksAction();
    }

    public function testExpireBusRegistrationAction()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(CliCommand\Bus\Expire::class))
            ->once()
            ->andReturn(new Command\Result());

        $this->sut->expireBusRegistrationAction();
    }

    public function testCleanUpVariationsAction()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(CliCommand\CleanUpAbandonedVariations::class))
            ->once()
            ->andReturn(new Command\Result());

        $this->sut->cleanUpVariationsAction();
    }

    public function testDataRetentionRuleActionPopulate()
    {
        $this->mockParamsPlugin(
            [
                'populate' => true,
            ]
        );

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\DataRetention\Populate::class))
            ->once()
            ->andReturn(new Command\Result());

        $result = $this->sut->dataRetentionRuleAction();

        $this->assertSame(0, $result->getErrorLevel());
    }

    public function testDataRetentionRuleActionDelete()
    {
        $this->mockParamsPlugin(
            [
                'delete' => true,
            ]
        );

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\DataRetention\DeleteEntities::class))
            ->once()
            ->andReturn(new Command\Result());

        $result = $this->sut->dataRetentionRuleAction();

        $this->assertSame(0, $result->getErrorLevel());
    }

    public function testDigitalContinuationRemindersAction()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\ContinuationDetail\DigitalSendReminders::class))
            ->once()
            ->andReturnUsing(
                function ($command) {
                    /** @var Command\ContinuationDetail\DigitalSendReminders $command */
                    $this->assertSame([], $command->getArrayCopy());
                    return new Command\Result();
                }
            );

        $result = $this->sut->digitalContinuationRemindersAction();
        $this->assertSame(0, $result->getErrorLevel());
    }

    public function testDatabaseMaintenanceAction()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\Organisation\FixIsIrfo::class))
            ->once()
            ->andReturnUsing(
                function ($command) {
                    /** @var Command\Organisation\FixIsIrfo $command */
                    $this->assertSame([], $command->getArrayCopy());
                    return new Command\Result();
                }
            )
            ->shouldReceive('handleCommand')
            ->with(m::type(Command\Organisation\FixIsUnlicenced::class))
            ->once()
            ->andReturnUsing(
                function ($command) {
                    /** @var Command\Organisation\FixIsUnlicenced $command */
                    $this->assertSame([], $command->getArrayCopy());
                    return new Command\Result();
                }
            );

        $result = $this->sut->databaseMaintenanceAction();
        $this->assertSame(0, $result->getErrorLevel());
    }

    public function testLastTmLetterAction()
    {
        $this->pm->shouldReceive('get')->with('params', null)->andReturn(false);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(\Dvsa\Olcs\Cli\Domain\Command\LastTmLetter::class))
            ->once()
            ->andReturn(new Command\Result());

        $this->sut->lastTmLetterAction();
    }

    /**
     * @param array $params
     * @param array $expected
     *
     * @dataProvider dpPermits
     */
    public function testPermitsAction($params, $expected)
    {
        $this->mockParamsPlugin($params);

        $this->mockCommandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type($expected['command']))
            ->once()
            ->andReturnUsing(
                function ($command) use ($expected) {
                    $this->assertEquals($expected['data'], $command->getArrayCopy());
                    return new Command\Result();
                }
            );

        $this->sut->permitsAction();
    }

    public function dpPermits()
    {
        return [
            'close expired windows' => [
                [
                    'close-expired-windows' => true,
                ],
                [
                    'command' => \Dvsa\Olcs\Cli\Domain\Command\Permits\CloseExpiredWindows::class,
                    'data' => ['since' => '-1 day'],
                ]
            ],
            'close expired windows within last month' => [
                [
                    'close-expired-windows' => true,
                    'since' => '-1 month',
                ],
                [
                    'command' => \Dvsa\Olcs\Cli\Domain\Command\Permits\CloseExpiredWindows::class,
                    'data' => ['since' => '-1 month'],
                ]
            ],
        ];
    }
}
