<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler;

use Doctrine\DBAL\Driver\PDOStatement;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Cli\Domain\Command\CompaniesHouseVsOlcsDiffsExport as Cmd;
use Dvsa\Olcs\Cli\Domain\CommandHandler\CompaniesHouseVsOlcsDiffsExport;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * @covers Dvsa\Olcs\Cli\Domain\CommandHandler\CompaniesHouseVsOlcsDiffsExport
 */
class CompaniesHouseVsOlcsDiffsExportTest extends CommandHandlerTestCase
{
    /** @var CompaniesHouseVsOlcsDiffsExport */
    protected $sut;

    /** @var  string */
    private $tmpPath;

    public function setUp(): void
    {
        $this->sut = new CompaniesHouseVsOlcsDiffsExport;

        //  mock repos
        $this->mockRepo('CompanyHouseVsOlcsDiffs', Repository\CompaniesHouseVsOlcsDiffs::class);

        //  mock config
        $this->tmpPath = vfsStream::setup('root')->url() . '/unit';

        $this->mockedSmServices['Config'] = [
            'ch-vs-olcs-export' => [
                'path' => 'unit_CfgPath',
            ],
        ];

        parent::setUp();
    }

    public function testMakeCsvsFromStatement()
    {
        $cmd = Cmd::create(
            [
                'path' => $this->tmpPath,
            ]
        );

        //  mock repository
        $this->mockRepoMethod('fetchOfficerDiffs');
        $this->mockRepoMethod('fetchAddressDiffs');
        $this->mockRepoMethod('fetchNameDiffs');
        $this->mockRepoMethod('fetchWithNotActiveStatus');

        //  call & check
        $actual = $this->sut->handleCommand($cmd);

        //  check file content
        $this->checkFileContent('CompanyOfficerDiffs');
        $this->checkFileContent('CompanyAddressDiffs');
        $this->checkFileContent('CompanyNameDiffs');
        $this->checkFileContent('CompanyNotActive');

        //  check messages
        $expectMsg =
            'Fetching data from DB for Company house and Organisation Name differences' .
            'create csv file: ' . $this->tmpPath . '/CompanyNameDiffs.csv' .
            'Fetching data from DB where Organisation not active in Company house' .
            'create csv file: ' . $this->tmpPath . '/CompanyNotActive.csv' .
            'Fetching data from DB for Company house and Organisation Address differences' .
            'create csv file: ' . $this->tmpPath . '/CompanyAddressDiffs.csv' .
            'Fetching data from DB for Company house and Organisation Officers differences' .
            'create csv file: ' . $this->tmpPath . '/CompanyOfficerDiffs.csv';

        static::assertEquals($expectMsg, implode('', $actual->toArray()['messages']));
    }

    private function mockRepoMethod($repoMethod)
    {
        $row1 = [
            'col1' => 'val11',
            'col2' => 'v"\'-/\,',
        ];
        $row2 = [
            'col1' => 'val21',
            'col2' => 'val22',
        ];

        $mockStmt = m::mock(PDOStatement::class)
            ->shouldReceive('fetch')->once()->andReturn($row1)
            ->shouldReceive('fetch')->once()->andReturn($row2)
            ->shouldReceive('fetch')->andReturn(false)
            ->getMock();

        $this->repoMap['CompanyHouseVsOlcsDiffs']
            ->shouldReceive($repoMethod)
            ->once()
            ->andReturn($mockStmt);
    }

    private function checkFileContent($fileName)
    {
        static::assertSame(
            'col1,col2' . "\n" .
            'val11,"v""\'-/\,"' . "\n" .
            'val21,val22' . "\n",
            file_get_contents($this->tmpPath . '/' . $fileName . '.csv')
        );
    }
}
