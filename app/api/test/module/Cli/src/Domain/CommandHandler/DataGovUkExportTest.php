<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler;

use Doctrine\DBAL\Driver\PDOStatement;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Cli\Domain\Command\DataGovUkExport as Cmd;
use Dvsa\Olcs\Cli\Domain\CommandHandler\DataGovUkExport;
use Dvsa\Olcs\Email\Service\Email;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use org\bovigo\vfs\vfsStream;

/**
 * @covers \Dvsa\Olcs\Cli\Domain\CommandHandler\DataGovUkExport
 */
class DataGovUkExportTest extends CommandHandlerTestCase
{
    /** @var DataGovUkExport */
    protected $sut;

    /** @var  string */
    private $tmpPath;

    /** @var  m\MockInterface */
    private $mockStmt;

    /** @var  m\MockInterface */
    private $mockEmailService;

    public function setUp()
    {
        $this->sut = new DataGovUkExport;

        //  mock repos
        $this->mockRepo('DataGovUk', Repository\DataGovUk::class);
        $this->mockRepo('TrafficArea', Repository\TrafficArea::class);
        $this->mockRepo('SystemParameter', Repository\SystemParameter::class);

        $this->mockStmt = m::mock(PDOStatement::class);

        //  mock config
        $this->mockedSmServices['Config'] = [
            'data-gov-uk-export' => [
                'path' => 'unit_CfgPath',
            ],
        ];

        $this->mockEmailService = m::mock(Email::class);
        $this->mockedSmServices['EmailService'] = $this->mockEmailService;

        parent::setUp();

        $this->tmpPath = vfsStream::setup('root')->url() . '/unit';
    }

    public function testInvalidReportException()
    {
        $cmd = Cmd::create(
            [
                'reportName' => 'INVALID',
                'path' => 'unit_Path',
            ]
        );

        //  expect
        $this->setExpectedException(\Exception::class, DataGovUkExport::ERR_INVALID_REPORT);

        //  call
        $this->sut->handleCommand($cmd);
    }

    public function testPsvOperatorListOk()
    {
        $fileName = 'PsvOperatorList_' . date('Y-m-d_h-i-s') . '.csv';

        $fileContent = '"Licence number",col1,col2
areaName1,val11,"v""\'-/\,"
areaName2,val21,val22
areaName1,val31,val32
';

        $row1 = [
            'Licence number' => 'areaName1',
            'col1' => 'val11',
            'col2' => 'v"\'-/\,',
        ];

        $row2 = [
            'Licence number' => 'areaName2',
            'col1' => 'val21',
            'col2' => 'val22',
        ];

        $row3 = [
            'Licence number' => 'areaName1',
            'col1' => 'val31',
            'col2' => 'val32',
        ];

        $this->mockStmt
            ->shouldReceive('fetch')->once()->andReturn($row1)
            ->shouldReceive('fetch')->once()->andReturn($row2)
            ->shouldReceive('fetch')->once()->andReturn($row3)
            ->shouldReceive('fetch')->andReturn(false);

        $this->repoMap['DataGovUk']
            ->shouldReceive('fetchPsvOperatorList')
            ->once()
            ->andReturn($this->mockStmt);

        $this->repoMap['SystemParameter']
            ->shouldReceive('fetchValue')
            ->with(SystemParameter::PSV_REPORT_EMAIL_LIST)
            ->once()
            ->andReturn('system-test@test.com');

        $this->mockEmailService
            ->shouldReceive('send')
            ->with(
                'notifications@vehicle-operator-licensing.service.gov.uk',
                'OLCS Notification',
                'system-test@test.com',
                'PSV Operator Licence Report',
                'Attached is the latest CSV export of all PSV Operators.',
                'Attached is the latest CSV export of all PSV Operators.',
                [],
                [],
                [
                    [
                        'content' => $fileContent,
                        'fileName' => $fileName,
                    ]
                ]
            )
            ->once()
            ->andReturn(true);

        //  call & check
        $cmd = Cmd::create(
            [
                'reportName' => DataGovUkExport::PSV_OPERATOR_LIST,
                'path' => $this->tmpPath,
            ]
        );

        $actual = $this->sut->handleCommand($cmd);

        $expectFile = $this->tmpPath . '/' . $fileName;

        $expectMsg =
            'Fetching data from DB for PSV Operators' .
            'create csv file: ' . $expectFile .
            'Sending report to: system-test@test.com';

        static::assertEquals(
            $expectMsg,
            implode('', $actual->toArray()['messages'])
        );
    }


    public function testPsvOperatorListNoEmailThrowException()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'No email address specified in system parameters for the PSV Report'
        );

        $fileName = 'PsvOperatorList_' . date('Y-m-d_h-i-s') . '.csv';

        $fileContent = '"Licence number",col1,col2
areaName1,val11,"v""\'-/\,"
areaName2,val21,val22
areaName1,val31,val32
';

        $row1 = [
            'Licence number' => 'areaName1',
            'col1' => 'val11',
            'col2' => 'v"\'-/\,',
        ];

        $row2 = [
            'Licence number' => 'areaName2',
            'col1' => 'val21',
            'col2' => 'val22',
        ];

        $row3 = [
            'Licence number' => 'areaName1',
            'col1' => 'val31',
            'col2' => 'val32',
        ];

        $this->mockStmt
            ->shouldReceive('fetch')->once()->andReturn($row1)
            ->shouldReceive('fetch')->once()->andReturn($row2)
            ->shouldReceive('fetch')->once()->andReturn($row3)
            ->shouldReceive('fetch')->andReturn(false);

        $this->repoMap['DataGovUk']
            ->shouldReceive('fetchPsvOperatorList')
            ->once()
            ->andReturn($this->mockStmt);

        $this->repoMap['SystemParameter']
            ->shouldReceive('fetchValue')
            ->with(SystemParameter::PSV_REPORT_EMAIL_LIST)
            ->once()
            ->andReturnNull();

        //  call & check
        $cmd = Cmd::create(
            [
                'reportName' => DataGovUkExport::PSV_OPERATOR_LIST,
                'path' => $this->tmpPath,
            ]
        );

        $this->sut->handleCommand($cmd);
    }

    public function testOperatorLicenceOk()
    {
        $cmd = Cmd::create(
            [
                'reportName' => DataGovUkExport::OPERATOR_LICENCE,
                'path' => $this->tmpPath,
            ]
        );

        //  mock repository
        $this->mockTrafficAreaRepo();

        $row1 = [
            'GeographicRegion' => 'areaName1',
            'col1' => 'val11',
            'col2' => 'v"\'-/\,',
        ];
        $row2 = [
            'GeographicRegion' => 'areaName2',
            'col1' => 'val21',
            'col2' => 'val22',
        ];
        $row3 = [
            'GeographicRegion' => 'areaName1',
            'col1' => 'val31',
            'col2' => 'val32',
        ];
        $this->mockStmt
            ->shouldReceive('fetch')->once()->andReturn($row1)
            ->shouldReceive('fetch')->once()->andReturn($row2)
            ->shouldReceive('fetch')->once()->andReturn($row3)
            ->shouldReceive('fetch')->andReturn(false);

        $this->repoMap['DataGovUk']
            ->shouldReceive('fetchOperatorLicences')
            ->once()
            ->andReturn($this->mockStmt);

        //  call & check
        $actual = $this->sut->handleCommand($cmd);

        $expectFile1 = $this->tmpPath . '/OLBSLicenceReport_areaName1.csv';
        $expectFile2 = $this->tmpPath . '/OLBSLicenceReport_areaName2.csv';

        $expectMsg =
            'Fetching data from DB for Operator Licences' .
            'create csv file: ' . $expectFile1 .
            'create csv file: ' . $expectFile2;

        static::assertEquals(
            $expectMsg,
            implode('', $actual->toArray()['messages'])
        );

        static::assertSame(
            'GeographicRegion,col1,col2' . PHP_EOL .
            'areaName1,val11,"v""\'-/\,"' . PHP_EOL .
            'areaName1,val31,val32' . PHP_EOL,
            file_get_contents($expectFile1)
        );
        static::assertSame(
            'GeographicRegion,col1,col2' . PHP_EOL . 'areaName2,val21,val22' . PHP_EOL,
            file_get_contents($expectFile2)
        );
    }

    public function testBugRegOnlyOk()
    {
        $cmd = Cmd::create(
            [
                'reportName' => DataGovUkExport::BUS_REGISTERED_ONLY,
                'path' => $this->tmpPath,
            ]
        );

        //  mock repository
        $this->mockTrafficAreaRepo();

        $row1 = [
            'Current Traffic Area' => 'areaId1',
            'col1' => 'val11',
            'col2' => 'v"\'-/\,',
        ];
        $row2 = [
            'Current Traffic Area' => 'areaId1',
            'col1' => 'val21',
            'col2' => 'val22',
        ];
        $this->mockStmt
            ->shouldReceive('fetch')->once()->andReturn($row1)
            ->shouldReceive('fetch')->once()->andReturn($row2)
            ->shouldReceive('fetch')->andReturn(false);

        $this->repoMap['DataGovUk']
            ->shouldReceive('fetchBusRegisteredOnly')
            ->once()
            ->andReturn($this->mockStmt);

        //  call & check
        $actual = $this->sut->handleCommand($cmd);

        $expectFile1 = $this->tmpPath . '/Bus_RegisteredOnly_areaId1.csv';

        $expectMsg =
            'Fetching data from DB for Bus Registered Only' .
            'create csv file: ' . $expectFile1;

        static::assertEquals(
            $expectMsg,
            implode('', $actual->toArray()['messages'])
        );

        static::assertSame(
            '"Current Traffic Area",col1,col2' . PHP_EOL .
            'areaId1,val11,"v""\'-/\,"' . PHP_EOL .
            'areaId1,val21,val22' . PHP_EOL,
            file_get_contents($expectFile1)
        );
    }

    public function testBugVariationOk()
    {
        $cmd = Cmd::create(
            [
                'reportName' => DataGovUkExport::BUS_VARIATION,
                'path' => $this->tmpPath,
            ]
        );

        //  mock repository
        $this->mockTrafficAreaRepo();

        $row1 = [
            'Current Traffic Area' => 'areaId1',
            'col1' => 'val11',
            'col2' => 'v"\'-/\,',
        ];
        $this->mockStmt
            ->shouldReceive('fetch')->once()->andReturn($row1)
            ->shouldReceive('fetch')->andReturn(false);

        $this->repoMap['DataGovUk']
            ->shouldReceive('fetchBusVariation')
            ->once()
            ->andReturn($this->mockStmt);

        //  call & check
        $actual = $this->sut->handleCommand($cmd);

        $expectFile1 = $this->tmpPath . '/Bus_Variation_areaId1.csv';

        $expectMsg =
            'Fetching data from DB for Bus Variation' .
            'create csv file: ' . $expectFile1;

        static::assertEquals(
            $expectMsg,
            implode('', $actual->toArray()['messages'])
        );

        static::assertSame(
            '"Current Traffic Area",col1,col2' . PHP_EOL .
            'areaId1,val11,"v""\'-/\,"' . PHP_EOL,
            file_get_contents($expectFile1)
        );
    }

    public function testTrafficAreaNotFound()
    {
        $this->repoMap['TrafficArea']
            ->shouldReceive('fetchAll')
            ->once()
            ->andReturn([]);

        //  expect
        $this->setExpectedException(\Exception::class, DataGovUkExport::ERR_NO_TRAFFIC_AREAS);

        //  call
        $this->sut->handleCommand(
            Cmd::create(['reportName' => DataGovUkExport::BUS_REGISTERED_ONLY])
        );
    }

    private function mockTrafficAreaRepo()
    {
        $this->repoMap['TrafficArea']
            ->shouldReceive('fetchAll')
            ->once()
            ->andReturn(
                [
                    m::mock(TrafficArea::class)
                        ->shouldReceive('getId')->atMost()->andReturn('areaId1')
                        ->shouldReceive('getName')->andReturn('areaName1')
                        ->getMock(),
                    m::mock(TrafficArea::class)
                        ->shouldReceive('getId')->zeroOrMoreTimes()->andReturn('areaId2')
                        ->shouldReceive('getName')->andReturn('areaName2')
                        ->getMock(),
                ]
            );
    }
}
