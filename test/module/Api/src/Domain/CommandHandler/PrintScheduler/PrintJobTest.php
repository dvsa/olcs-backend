<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\PrintScheduler;

use Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler\PrintJob as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\PrintJob as Cmd;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\Queue\Queue;

/**
 * PrintJobTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class PrintJobTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = m::mock(CommandHandler::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $this->mockRepo('Document', \Dvsa\Olcs\Api\Domain\Repository\Document::class);
        $this->mockRepo('User', \Dvsa\Olcs\Api\Domain\Repository\User::class);
        $this->mockRepo('SystemParameter', \Dvsa\Olcs\Api\Domain\Repository\SystemParameter::class);
        $this->mockRepo('Printer', \Dvsa\Olcs\Api\Domain\Repository\Printer::class);

        $mockFileUploader = m::mock(\Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader::class);
        $this->mockedSmServices['FileUploader'] = $mockFileUploader;

        $this->mockedSmServices['Config'] = ['print' => ['server' => 'PRINT_SERVER']];

        $this->mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)->makePartial();
        $this->mockUser->setLoginId('LOGIN_ID');
        $this->repoMap['User']->shouldReceive('fetchById')->with('USER_ID')->andReturn($this->mockUser);

        $mockDocument = m::mock(\Dvsa\Olcs\Api\Entity\Doc\Document::class)->makePartial();
        $mockDocument->setIdentifier('IDENTIFIER');
        $mockDocument->setFilename('FILENAME');
        $mockDocument->setDescription('DESC');
        $this->repoMap['Document']->shouldReceive('fetchById')->with('DOC_ID')->once()->andReturn($mockDocument);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
        ];

        $this->references = [
            \Dvsa\Olcs\Api\Entity\Licence\Licence::class => [
                34 => m::mock(\Dvsa\Olcs\Api\Entity\Licence\Licence::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommandNoUser()
    {
        $command = Cmd::create(['id' => 'QUEUE_ID', 'document' => 'DOC_ID', 'title' => 'JOB', 'user' => '']);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(\Dvsa\Olcs\Api\Entity\System\SystemParameter::SELFSERVE_USER_PRINTER)->once()->andReturn('QUEUE1');

        $mockFile = m::mock(\Dvsa\Olcs\DocumentShare\Data\Object\File::class);
        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER')->once()
            ->andReturn($mockFile);

        $this->sut->shouldReceive('createTmpFile')->with($mockFile, 'QUEUE_ID', 'FILENAME')->once()
            ->andReturn('TEMP_FILE.rtf');

        $this->expectPrintFile(0, 0, 'Anonymous');

        $this->sut->shouldReceive('deleteTempFiles')->with('TEMP_FILE.rtf')->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Printed successfully"], $result->getMessages());
    }

    public function testHandleCommandSelfserveUser()
    {
        $command = Cmd::create(['id' => 'QUEUE_ID', 'document' => 'DOC_ID', 'title' => 'JOB', 'user' => 'USER_ID']);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(\Dvsa\Olcs\Api\Entity\System\SystemParameter::SELFSERVE_USER_PRINTER)->once()->andReturn('QUEUE1');

        $mockFile = m::mock(\Dvsa\Olcs\DocumentShare\Data\Object\File::class);
        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER')->once()
            ->andReturn($mockFile);

        $this->sut->shouldReceive('createTmpFile')->with($mockFile, 'QUEUE_ID', 'FILENAME')->once()
            ->andReturn('TEMP_FILE.rtf');

        $this->expectPrintFile();

        $this->sut->shouldReceive('deleteTempFiles')->with('TEMP_FILE.rtf')->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Printed successfully"], $result->getMessages());
    }

    public function testHandleCommandPdfCreateError()
    {
        $command = Cmd::create(['id' => 'QUEUE_ID', 'document' => 'DOC_ID', 'title' => 'JOB', 'user' => 'USER_ID']);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(\Dvsa\Olcs\Api\Entity\System\SystemParameter::SELFSERVE_USER_PRINTER)->once()->andReturn('QUEUE1');

        $mockFile = m::mock(\Dvsa\Olcs\DocumentShare\Data\Object\File::class);
        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER')->once()
            ->andReturn($mockFile);

        $this->sut->shouldReceive('createTmpFile')->with($mockFile, 'QUEUE_ID', 'FILENAME')->once()
            ->andReturn('TEMP_FILE.rtf');

        $this->expectPrintFile(1);

        $this->sut->shouldReceive('deleteTempFiles')->with('TEMP_FILE.rtf')->once();

        $this->setExpectedException(
            \Dvsa\Olcs\Api\Domain\Exception\NotReadyException::class,
            'Print service not available: OUTPUT PDF'
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandPrintCreateError()
    {
        $command = Cmd::create(['id' => 'QUEUE_ID', 'document' => 'DOC_ID', 'title' => 'JOB', 'user' => 'USER_ID']);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(\Dvsa\Olcs\Api\Entity\System\SystemParameter::SELFSERVE_USER_PRINTER)->once()->andReturn('QUEUE1');

        $mockFile = m::mock(\Dvsa\Olcs\DocumentShare\Data\Object\File::class);
        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER')->once()
            ->andReturn($mockFile);

        $this->sut->shouldReceive('createTmpFile')->with($mockFile, 'QUEUE_ID', 'FILENAME')->once()
            ->andReturn('TEMP_FILE.rtf');

        $this->expectPrintFile(0, 1);

        $this->sut->shouldReceive('deleteTempFiles')->with('TEMP_FILE.rtf')->once();

        $this->setExpectedException(
            \Dvsa\Olcs\Api\Domain\Exception\NotReadyException::class,
            'Print service not available: OUTPUT LPR'
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandInternalUser()
    {
        $command = Cmd::create(['id' => 'QUEUE_ID', 'document' => 'DOC_ID', 'title' => 'JOB', 'user' => 'USER_ID']);

        $team = new \Dvsa\Olcs\Api\Entity\User\Team();
        $printer = new \Dvsa\Olcs\Api\Entity\PrintScan\Printer();
        $printer->setPrinterName('QUEUE1');
        $teamPrinter = new \Dvsa\Olcs\Api\Entity\PrintScan\TeamPrinter($team, $printer);
        $team->addTeamPrinters($teamPrinter);
        $this->mockUser->setTeam($team);

        $mockFile = m::mock(\Dvsa\Olcs\DocumentShare\Data\Object\File::class);
        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER')->once()
            ->andReturn($mockFile);

        $this->sut->shouldReceive('createTmpFile')->with($mockFile, 'QUEUE_ID', 'FILENAME')->once()
            ->andReturn('TEMP_FILE.rtf');

        $this->expectPrintFile();

        $this->sut->shouldReceive('deleteTempFiles')->with('TEMP_FILE.rtf')->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Printed successfully"], $result->getMessages());
    }

    public function testHandleCommandCannotDownloadFile()
    {
        $command = Cmd::create(['id' => 'QUEUE_ID', 'document' => 'DOC_ID', 'title' => 'JOB', 'user' => 'USER_ID']);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(\Dvsa\Olcs\Api\Entity\System\SystemParameter::SELFSERVE_USER_PRINTER)->once()->andReturn('QUEUE1');

        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER')->once()
            ->andReturn(null);

        $this->setExpectedException(
            \Dvsa\Olcs\Api\Domain\Exception\Exception::class,
            "Can't find document"
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandInternalUserNoPrinter()
    {
        $command = Cmd::create(['id' => 'QUEUE_ID', 'document' => 'DOC_ID', 'title' => 'JOB', 'user' => 'USER_ID']);

        $team = new \Dvsa\Olcs\Api\Entity\User\Team();
        $this->mockUser->setTeam($team);

        $this->setExpectedException(
            \Dvsa\Olcs\Api\Domain\Exception\Exception::class,
            'Cannot find printer for User LOGIN_ID'
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandStubLicence()
    {
        $command = Cmd::create(['id' => 'QUEUE_ID', 'document' => 'DOC_ID', 'title' => 'JOB', 'user' => 'USER_ID']);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(\Dvsa\Olcs\Api\Entity\System\SystemParameter::SELFSERVE_USER_PRINTER)->once()
            ->andReturn('TESTING-STUB-LICENCE:34');

        $this->repoMap['Document']->shouldReceive('save')->once()->andReturnUsing(
            function ($document) {
                $this->assertSame('PRINT DESC', $document->getDescription());
                $this->assertSame(34, $document->getLicence()->getId());
            }
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Printed successfully (stub to licence 34)"], $result->getMessages());
    }

    private function expectPrintFile($commandPdfResult = 0, $commandLprResult = 0, $userName = 'LOGIN_ID')
    {
        $this->sut->shouldReceive('executeCommand')
            ->with("soffice --headless --convert-to pdf:writer_pdf_Export --outdir /tmp 'TEMP_FILE.rtf'", [], null)
            ->once()
            ->andReturnUsing(
                function ($command, &$output, &$result) use ($commandPdfResult) {
                    $result = $commandPdfResult;
                    $output = ['OUTPUT PDF'];
                }
            );

        if ($commandPdfResult !== 0) {
            return;
        }

        $this->sut->shouldReceive('executeCommand')
            ->with("lpr 'TEMP_FILE.pdf' -H 'PRINT_SERVER' -C 'TEMP_FILE.rtf' -h -P 'QUEUE1' -U '{$userName}'", [], null)
            ->once()
            ->andReturnUsing(
                function ($command, &$output, &$result) use ($commandLprResult) {
                    $result = $commandLprResult;
                    $output = ['OUTPUT LPR'];
                }
            );
    }
}
