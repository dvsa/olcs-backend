<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler;

use Doctrine\DBAL\Driver\PDOStatement;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Cli\Domain\Command\DataGovUkExport as Cmd;
use Dvsa\Olcs\Cli\Domain\CommandHandler\DataGovUkExport;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * @covers Dvsa\Olcs\Cli\Domain\CommandHandler\DataGovUkExport
 */
class DataGovUkExportTest extends CommandHandlerTestCase
{
    /** @var DataGovUkExport */
    protected $sut;

    /** @var  string */
    private $tmpPath;
    /** @var  m\MockInterface */
    private $mockStmt;
    /** @var  DateTime */
    private $now;

    public function setUp()
    {
        $this->sut = new DataGovUkExport;

        //  mock repos
        $this->mockRepo('DataGovUk', Repository\DataGovUk::class);

        $this->mockStmt = m::mock(PDOStatement::class);
        $this->repoMap['DataGovUk']
            ->shouldReceive('fetchOperatorLicences')
            ->atMost(1)
            ->andReturn($this->mockStmt)
            ->getMock();

        //  mock config
        $this->mockedSmServices['Config'] = [
            'data-gov-uk-export' => [
                'path' => 'unit_CfgPath',
            ],
        ];

        parent::setUp();

        $this->now = new DateTime;

        $this->tmpPath = vfsStream::setup('root')->url() . '/unit';
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testInvalidReportExpection()
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

    public function testOk()
    {
        $cmd = Cmd::create(
            [
                'reportName' => DataGovUkExport::OPERATOR_LICENCE,
                'path' => $this->tmpPath,
            ]
        );

        //  mock repository
        $row = [
            'col1' => 'val1',
            'col2' => 'v"\'-/\,',
        ];
        $row2 = [
            'col1' => 'val21',
            'col2' => 'val22',
        ];
        $this->mockStmt
            ->shouldReceive('fetch')->once()->andReturn($row)
            ->shouldReceive('fetch')->once()->andReturn($row2)
            ->shouldReceive('fetch')->andReturn(false);

        //  call & check
        $actual = $this->sut->handleCommand($cmd);

        $expectFile = $this->tmpPath . '/' .
            DataGovUkExport::OPERATOR_LICENCE . '-' .
            $this->now->format(DataGovUkExport::FILE_DATETIME_FORMAT) . '.csv';

        $expectMsg =
            "Fetching data from DB" .
            "Export data to csv file " . $expectFile;

        static::assertEquals(
            $expectMsg,
            implode('', $actual->toArray()['messages'])
        );

        static::assertSame(
            'col1,col2' . PHP_EOL . 'val1,"v""\'-/\,"' . PHP_EOL . 'val21,val22' . PHP_EOL,
            file_get_contents($expectFile)
        );
    }

    public function testExceptionCreateDir()
    {
        $cmd = Cmd::create(
            [
                'reportName' => DataGovUkExport::OPERATOR_LICENCE,
                'path' => $this->tmpPath,
            ]
        );

        //  create file with dir name
        $fh = fopen($this->tmpPath, 'w');
        fclose($fh);

        //  expect
        $expectFile = $this->tmpPath . '/' .
            DataGovUkExport::OPERATOR_LICENCE . '-' .
            $this->now->format(DataGovUkExport::FILE_DATETIME_FORMAT) . '.csv';

        $this->setExpectedException(\Exception::class, DataGovUkExport::ERR_CANT_CREATE_DIR . $expectFile);

        //  call & check
        $this->sut->handleCommand($cmd);
    }

    public function testExceptionCreateFile()
    {
        $cmd = Cmd::create(
            [
                'reportName' => DataGovUkExport::OPERATOR_LICENCE,
                'path' => $this->tmpPath,
            ]
        );

        //  create file with dir name
        $expectFile = $this->tmpPath . '/' .
            DataGovUkExport::OPERATOR_LICENCE . '-' .
            $this->now->format(DataGovUkExport::FILE_DATETIME_FORMAT) . '.csv';

        /** @noinspection MkdirRaceConditionInspection */
        mkdir($expectFile, 0750, true);

        //  expect
        $this->setExpectedException(\Exception::class, DataGovUkExport::ERR_CANT_CREATE_FILE . $expectFile);

        //  call & check
        $this->sut->handleCommand($cmd);
    }
}
