<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\PrintScheduler;

use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\PrintJob as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler\PrintJob as CommandHandler;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * PrintJobTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class PrintJobTest extends CommandHandlerTestCase
{
    /** @var  CommandHandler | m\MockInterface */
    protected $sut;

    /** @var m\MockInterface | \Dvsa\Olcs\Api\Service\ConvertToPdf\WebServiceClient  */
    private $convertToPdfService;
    /** @var m\MockInterface | \Dvsa\Olcs\Api\Entity\User\User */
    private $mockUser;

    public function setUp()
    {
        $this->sut = m::mock(CommandHandler::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $this->mockRepo('Document', \Dvsa\Olcs\Api\Domain\Repository\Document::class);
        $this->mockRepo('User', \Dvsa\Olcs\Api\Domain\Repository\User::class);
        $this->mockRepo('SystemParameter', \Dvsa\Olcs\Api\Domain\Repository\SystemParameter::class);
        $this->mockRepo('Printer', \Dvsa\Olcs\Api\Domain\Repository\Printer::class);

        $mockFileUploader = m::mock(\Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader::class);
        $this->mockedSmServices['FileUploader'] = $mockFileUploader;

        $this->mockedSmServices['Config'] = [
            'print' => [
                'server' => 'PRINT_SERVER',
                'options' => [
                    'user' => 'PRINT_USER',
                ],
            ]
        ];

        $this->convertToPdfService = m::mock();
        $this->mockedSmServices['ConvertToPdf'] = $this->convertToPdfService;

        $this->mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)->makePartial();
        $this->mockUser->setLoginId('LOGIN_ID');
        $this->repoMap['User']->shouldReceive('fetchById')->with('USER_ID')->andReturn($this->mockUser);

        /** @var \Dvsa\Olcs\Api\Entity\Doc\Document $mockDocument */
        $mockDocument = m::mock(\Dvsa\Olcs\Api\Entity\Doc\Document::class)->makePartial();
        $mockDocument->setIdentifier('IDENTIFIER');
        $mockDocument->setFilename('FILENAME');
        $mockDocument->setDescription('DESC');
        $this->repoMap['Document']->shouldReceive('fetchById')->with('DOC_ID')->once()->andReturn($mockDocument);

        /** @var \Dvsa\Olcs\Api\Entity\Doc\Document $mockDocument */
        $mockDocument2 = m::mock(\Dvsa\Olcs\Api\Entity\Doc\Document::class)->makePartial();
        $mockDocument2->setIdentifier('IDENTIFIER2');
        $mockDocument2->setFilename('FILENAME2');
        $mockDocument2->setDescription('DESC2');
        $this->repoMap['Document']->shouldReceive('fetchById')->with('DOC2_ID')->andReturn($mockDocument2);

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
        $command = Cmd::create(
            [
                'id' => 'QUEUE_ID',
                'documents' => ['DOC_ID'],
                'title' => 'JOB',
                'copies' => 999,
            ]
        );

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(\Dvsa\Olcs\Api\Entity\System\SystemParameter::SELFSERVE_USER_PRINTER)->once()->andReturn('QUEUE1');

        $mockFile = m::mock(\Dvsa\Olcs\DocumentShare\Data\Object\File::class);
        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER')->once()
            ->andReturn($mockFile);

        $this->sut->shouldReceive('createTmpFile')
            ->with($mockFile, CommandHandler::TEMP_FILE_PREFIX . '-QUEUE_ID-', 'FILENAME')
            ->once()
            ->andReturn('TEMP_FILE.rtf');

        $this->expectPrintFile(0, true, 0, 'PRINT_USER', 999);

        $this->sut->shouldReceive('deleteTempFiles')->withNoArgs()->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Printed successfully"], $result->getMessages());
    }

    public function testHandleCommandConvertUsingWebService()
    {
        $this->sut->setConfig(
            [
                'print' => ['server' => 'PRINT_SERVER'],
                'convert_to_pdf' => ['uri' => 'http://web.com:8080/foo']
            ]
        );

        $command = Cmd::create(['id' => 'QUEUE_ID', 'documents' => ['DOC_ID'], 'title' => 'JOB', 'user' => '']);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(\Dvsa\Olcs\Api\Entity\System\SystemParameter::SELFSERVE_USER_PRINTER)->once()->andReturn('QUEUE1');

        $mockFile = m::mock(\Dvsa\Olcs\DocumentShare\Data\Object\File::class);
        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER')->once()
            ->andReturn($mockFile);

        $this->sut->shouldReceive('createTmpFile')
            ->with($mockFile, CommandHandler::TEMP_FILE_PREFIX . '-QUEUE_ID-', 'FILENAME')
            ->once()
            ->andReturn('TEMP_FILE.rtf');

        $this->convertToPdfService->shouldReceive('convert')->with('TEMP_FILE.rtf', 'TEMP_FILE.pdf')->once();

        $this->expectLpr('Anonymous', 0, true, 1);

        $this->sut->shouldReceive('deleteTempFiles')->withNoArgs()->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Printed successfully"], $result->getMessages());
    }

    public function testHandleCommandConvertUsingWebServiceError()
    {
        $this->sut->setConfig(
            [
                'print' => ['server' => 'PRINT_SERVER'],
                'convert_to_pdf' => ['uri' => 'http://web.com:8080/foo']
            ]
        );

        $command = Cmd::create(['id' => 'QUEUE_ID', 'documents' => ['DOC_ID'], 'title' => 'JOB', 'user' => '']);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(\Dvsa\Olcs\Api\Entity\System\SystemParameter::SELFSERVE_USER_PRINTER)->once()->andReturn('QUEUE1');

        $mockFile = m::mock(\Dvsa\Olcs\DocumentShare\Data\Object\File::class);
        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER')->once()
            ->andReturn($mockFile);

        $this->sut->shouldReceive('createTmpFile')
            ->with($mockFile, CommandHandler::TEMP_FILE_PREFIX . '-QUEUE_ID-', 'FILENAME')
            ->once()
            ->andReturn('TEMP_FILE.rtf');

        $this->convertToPdfService->shouldReceive('convert')->with('TEMP_FILE.rtf', 'TEMP_FILE.pdf')->once()
            ->andThrow(\Dvsa\Olcs\Api\Domain\Exception\RestResponseException::class, 'TEST MESSAGE');

        $this->sut->shouldReceive('deleteTempFiles')->withNoArgs()->once();

        $this->expectException(
            \Dvsa\Olcs\Api\Domain\Exception\NotReadyException::class,
            'Error generating the PDF TEMP_FILE.rtf : TEST MESSAGE'
        );
        $this->sut->handleCommand($command);
    }

    /**
     * Test https://jira.i-env.net/browse/OLCS-15140 Convert to PDF timeout
     */
    public function testHandleCommandConvertUsingWebServiceTimeout()
    {
        $this->sut->setConfig(
            [
                'print' => ['server' => 'PRINT_SERVER'],
                'convert_to_pdf' => ['uri' => 'http://web.com:8080/foo']
            ]
        );

        $command = Cmd::create(['id' => 'QUEUE_ID', 'documents' => ['DOC_ID'], 'title' => 'JOB', 'user' => '']);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(\Dvsa\Olcs\Api\Entity\System\SystemParameter::SELFSERVE_USER_PRINTER)->once()->andReturn('QUEUE1');

        $mockFile = m::mock(\Dvsa\Olcs\DocumentShare\Data\Object\File::class);
        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER')->once()
            ->andReturn($mockFile);

        $this->sut->shouldReceive('createTmpFile')
            ->with($mockFile, CommandHandler::TEMP_FILE_PREFIX . '-QUEUE_ID-', 'FILENAME')
            ->once()
            ->andReturn('TEMP_FILE.rtf');

        $this->convertToPdfService->shouldReceive('convert')->with('TEMP_FILE.rtf', 'TEMP_FILE.pdf')->once()
            ->andThrow(\Zend\Http\Client\Adapter\Exception\TimeoutException::class, 'Timeout');

        $this->sut->shouldReceive('deleteTempFiles')->withNoArgs()->once();

        $this->expectException(
            \Dvsa\Olcs\Api\Domain\Exception\NotReadyException::class,
            'Error generating the PDF TEMP_FILE.rtf : Timeout'
        );
        $this->sut->handleCommand($command);
    }

    public function testHandleCommandSelfserveUser()
    {
        $command = Cmd::create(['id' => 'QUEUE_ID', 'documents' => ['DOC_ID'], 'title' => 'JOB', 'user' => 'USER_ID']);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(\Dvsa\Olcs\Api\Entity\System\SystemParameter::SELFSERVE_USER_PRINTER)->once()->andReturn('QUEUE1');

        $mockFile = m::mock(\Dvsa\Olcs\DocumentShare\Data\Object\File::class);
        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER')->once()
            ->andReturn($mockFile);

        $this->sut->shouldReceive('createTmpFile')
            ->with($mockFile, CommandHandler::TEMP_FILE_PREFIX . '-QUEUE_ID-', 'FILENAME')
            ->once()
            ->andReturn('TEMP_FILE.rtf');

        $this->expectPrintFile();

        $this->sut->shouldReceive('deleteTempFiles')->withNoArgs()->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Printed successfully"], $result->getMessages());
    }

    public function testHandleCommandPdfCreateError()
    {
        $command = Cmd::create(['id' => 'QUEUE_ID', 'documents' => ['DOC_ID'], 'title' => 'JOB', 'user' => 'USER_ID']);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(\Dvsa\Olcs\Api\Entity\System\SystemParameter::SELFSERVE_USER_PRINTER)->once()->andReturn('QUEUE1');

        $mockFile = m::mock(\Dvsa\Olcs\DocumentShare\Data\Object\File::class);
        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER')->once()
            ->andReturn($mockFile);

        $this->sut->shouldReceive('createTmpFile')
            ->with($mockFile, CommandHandler::TEMP_FILE_PREFIX . '-QUEUE_ID-', 'FILENAME')
            ->once()
            ->andReturn('TEMP_FILE.rtf');

        $this->expectPrintFile(1);

        $this->sut->shouldReceive('deleteTempFiles')->withNoArgs()->once();

        $this->expectException(
            \Dvsa\Olcs\Api\Domain\Exception\NotReadyException::class,
            'Error generating the PDF : OUTPUT PDF'
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandPdfMissing()
    {
        $command = Cmd::create(['id' => 'QUEUE_ID', 'documents' => ['DOC_ID'], 'title' => 'JOB', 'user' => 'USER_ID']);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(\Dvsa\Olcs\Api\Entity\System\SystemParameter::SELFSERVE_USER_PRINTER)->once()->andReturn('QUEUE1');

        $mockFile = m::mock(\Dvsa\Olcs\DocumentShare\Data\Object\File::class);
        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER')->once()
            ->andReturn($mockFile);

        $this->sut->shouldReceive('createTmpFile')
            ->with($mockFile, CommandHandler::TEMP_FILE_PREFIX . '-QUEUE_ID-', 'FILENAME')
            ->once()
            ->andReturn('TEMP_FILE.rtf');

        $this->expectPrintFile(0, false);

        $this->sut->shouldReceive('deleteTempFiles')->withNoArgs()->once();

        $this->expectException(
            \Dvsa\Olcs\Api\Domain\Exception\NotReadyException::class,
            'PDF file does not exist : TEMP_FILE.pdf'
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandPrintCreateError()
    {
        $command = Cmd::create(['id' => 'QUEUE_ID', 'documents' => ['DOC_ID'], 'title' => 'JOB', 'user' => 'USER_ID']);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(\Dvsa\Olcs\Api\Entity\System\SystemParameter::SELFSERVE_USER_PRINTER)->once()->andReturn('QUEUE1');

        $mockFile = m::mock(\Dvsa\Olcs\DocumentShare\Data\Object\File::class);
        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER')->once()
            ->andReturn($mockFile);

        $this->sut->shouldReceive('createTmpFile')
            ->with($mockFile, CommandHandler::TEMP_FILE_PREFIX . '-QUEUE_ID-', 'FILENAME')
            ->once()
            ->andReturn('TEMP_FILE.rtf');

        $this->expectPrintFile(0, true, 1);

        $this->sut->shouldReceive('deleteTempFiles')->withNoArgs()->once();

        $this->expectException(
            \Dvsa\Olcs\Api\Domain\Exception\NotReadyException::class,
            'Error executing lpr command : OUTPUT LPR'
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandInternalUser()
    {
        $command = Cmd::create(['id' => 'QUEUE_ID', 'documents' => ['DOC_ID'], 'title' => 'JOB', 'user' => 'USER_ID']);

        $team = new \Dvsa\Olcs\Api\Entity\User\Team();
        $printer = new \Dvsa\Olcs\Api\Entity\PrintScan\Printer();
        $printer->setPrinterName('QUEUE1');
        $teamPrinter = new \Dvsa\Olcs\Api\Entity\PrintScan\TeamPrinter($team, $printer);
        $team->addTeamPrinters($teamPrinter);
        $this->mockUser->setTeam($team);

        $mockFile = m::mock(\Dvsa\Olcs\DocumentShare\Data\Object\File::class);
        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER')->once()
            ->andReturn($mockFile);

        $this->sut->shouldReceive('createTmpFile')
            ->with($mockFile, CommandHandler::TEMP_FILE_PREFIX . '-QUEUE_ID-', 'FILENAME')
            ->once()
            ->andReturn('TEMP_FILE.rtf');

        $this->expectPrintFile();

        $this->sut->shouldReceive('deleteTempFiles')->withNoArgs()->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Printed successfully"], $result->getMessages());
    }

    public function testHandleCommandInternalUserMultiDocs()
    {
        $command = Cmd::create(
            ['id' => 'QUEUE_ID', 'documents' => ['DOC_ID', 'DOC2_ID'], 'title' => 'JOB', 'user' => 'USER_ID']
        );

        $team = new \Dvsa\Olcs\Api\Entity\User\Team();
        $printer = new \Dvsa\Olcs\Api\Entity\PrintScan\Printer();
        $printer->setPrinterName('QUEUE1');
        $teamPrinter = new \Dvsa\Olcs\Api\Entity\PrintScan\TeamPrinter($team, $printer);
        $team->addTeamPrinters($teamPrinter);
        $this->mockUser->setTeam($team);

        // 1st file
        $mockFile = m::mock(\Dvsa\Olcs\DocumentShare\Data\Object\File::class);
        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER')->once()
            ->andReturn($mockFile);

        $this->sut->shouldReceive('createTmpFile')
            ->with($mockFile, CommandHandler::TEMP_FILE_PREFIX . '-QUEUE_ID-', 'FILENAME')
            ->once()
            ->andReturn('TEMP_FILE.rtf');

        $this->sut->shouldReceive('executeCommand')
            ->with("soffice --headless --convert-to pdf:writer_pdf_Export --outdir /tmp 'TEMP_FILE.rtf' 2>&1", [], null)
            ->once()
            ->andReturnUsing(
                function ($command, &$output, &$result) {
                    $result = 0;
                    $output = ['OUTPUT PDF'];
                }
            );

        // 2nd file
        $mockFile2 = m::mock(\Dvsa\Olcs\DocumentShare\Data\Object\File::class);
        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER2')->once()
            ->andReturn($mockFile2);

        $this->sut->shouldReceive('createTmpFile')
            ->with($mockFile2, CommandHandler::TEMP_FILE_PREFIX . '-QUEUE_ID-', 'FILENAME2')
            ->once()
            ->andReturn('TEMP_FILE2.rtf');

        $this->sut->shouldReceive('executeCommand')
            ->with(
                "soffice --headless --convert-to pdf:writer_pdf_Export --outdir /tmp 'TEMP_FILE2.rtf' 2>&1",
                [],
                null
            )
            ->once()
            ->andReturnUsing(
                function ($command, &$output, &$result) {
                    $result = 0;
                    $output = ['OUTPUT PDF2'];
                }
            );

        // both pdf files to be merged into one
        $this->sut->shouldReceive('executeCommand')
            ->with(
                "pdfunite 'TEMP_FILE.pdf' 'TEMP_FILE2.pdf' '/tmp/PrintJob-QUEUE_ID-print.pdf' 2>&1",
                [],
                null
            )
            ->once()
            ->andReturnUsing(
                function ($command, &$output, &$result) {
                    $result = 0;
                    $output = ['PDF FILES MERGED'];
                }
            );

        // the file to be printed
        $this->sut->shouldReceive('fileExists')->with('/tmp/PrintJob-QUEUE_ID-print.pdf')->once()->andReturn(true);

        $this->sut->shouldReceive('executeCommand')
            ->with(
                "lpr '/tmp/PrintJob-QUEUE_ID-print.pdf'" .
                " -H 'PRINT_SERVER'" .
                " -C 'PrintJob-QUEUE_ID-print.pdf'" .
                " -h -P 'QUEUE1'" .
                " -U 'PRINT_USER'".
                " -#1" .
                " -o collate=true" .
                " 2>&1",
                [],
                null
            )->once()
            ->andReturnUsing(
                function ($command, &$output, &$result) {
                    $result = 0;
                    $output = ['OUTPUT LPR'];
                }
            );

        $this->sut->shouldReceive('deleteTempFiles')->withNoArgs()->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['Printed successfully'], $result->getMessages());
    }

    public function testHandleCommandInternalUserMultiDocsMergeError()
    {
        $command = Cmd::create(
            ['id' => 'QUEUE_ID', 'documents' => ['DOC_ID', 'DOC2_ID'], 'title' => 'JOB', 'user' => 'USER_ID']
        );

        $team = new \Dvsa\Olcs\Api\Entity\User\Team();
        $printer = new \Dvsa\Olcs\Api\Entity\PrintScan\Printer();
        $printer->setPrinterName('QUEUE1');
        $teamPrinter = new \Dvsa\Olcs\Api\Entity\PrintScan\TeamPrinter($team, $printer);
        $team->addTeamPrinters($teamPrinter);
        $this->mockUser->setTeam($team);

        // 1st file
        $mockFile = m::mock(\Dvsa\Olcs\DocumentShare\Data\Object\File::class);
        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER')->once()
            ->andReturn($mockFile);

        $this->sut->shouldReceive('createTmpFile')
            ->with($mockFile, CommandHandler::TEMP_FILE_PREFIX . '-QUEUE_ID-', 'FILENAME')
            ->once()
            ->andReturn('TEMP_FILE.rtf');

        $this->sut->shouldReceive('executeCommand')
            ->with("soffice --headless --convert-to pdf:writer_pdf_Export --outdir /tmp 'TEMP_FILE.rtf' 2>&1", [], null)
            ->once()
            ->andReturnUsing(
                function ($command, &$output, &$result) {
                    $result = 0;
                    $output = ['OUTPUT PDF'];
                }
            );

        // 2nd file
        $mockFile2 = m::mock(\Dvsa\Olcs\DocumentShare\Data\Object\File::class);
        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER2')->once()
            ->andReturn($mockFile2);

        $this->sut->shouldReceive('createTmpFile')
            ->with($mockFile2, CommandHandler::TEMP_FILE_PREFIX . '-QUEUE_ID-', 'FILENAME2')
            ->once()
            ->andReturn('TEMP_FILE2.rtf');

        $this->sut->shouldReceive('executeCommand')
            ->with(
                "soffice --headless --convert-to pdf:writer_pdf_Export --outdir /tmp 'TEMP_FILE2.rtf' 2>&1",
                [],
                null
            )
            ->once()
            ->andReturnUsing(
                function ($command, &$output, &$result) {
                    $result = 0;
                    $output = ['OUTPUT PDF2'];
                }
            );

        // both pdf files to be merged into one
        $this->sut->shouldReceive('executeCommand')
            ->with(
                "pdfunite 'TEMP_FILE.pdf' 'TEMP_FILE2.pdf' '/tmp/PrintJob-QUEUE_ID-print.pdf' 2>&1",
                [],
                null
            )
            ->once()
            ->andReturnUsing(
                function ($command, &$output, &$result) {
                    $result = 1;
                    $output = ['PDF MERGE ERROR'];
                }
            );

        // the file NOT to be printed
        $this->sut->shouldReceive('fileExists')->never();

        $this->sut->shouldReceive('executeCommand')
            ->with(
                "lpr '/tmp/PrintJob-QUEUE_ID-print.pdf'" .
                " -H 'PRINT_SERVER'" .
                " -C 'PrintJob-QUEUE_ID-print.pdf'" .
                " -h -P 'QUEUE1'" .
                " -U 'PRINT_USER'".
                " -#1" .
                " -o collate=true" .
                " 2>&1",
                [],
                null
            )->never();

        $this->sut->shouldReceive('deleteTempFiles')->withNoArgs()->once();

        $this->expectException(\Dvsa\Olcs\Api\Domain\Exception\NotReadyException::class);

        $result = $this->sut->handleCommand($command);
    }

    public function testHandleCommandCannotDownloadFile()
    {
        $command = Cmd::create(['id' => 'QUEUE_ID', 'documents' => ['DOC_ID'], 'title' => 'JOB', 'user' => 'USER_ID']);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(\Dvsa\Olcs\Api\Entity\System\SystemParameter::SELFSERVE_USER_PRINTER)->once()->andReturn('QUEUE1');

        $this->mockedSmServices['FileUploader']->shouldReceive('download')->with('IDENTIFIER')->once()
            ->andReturn(null);

        $this->expectException(
            \Dvsa\Olcs\Api\Domain\Exception\Exception::class,
            "Can't find document"
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandInternalUserNoPrinter()
    {
        $command = Cmd::create(['id' => 'QUEUE_ID', 'documents' => ['DOC_ID'], 'title' => 'JOB', 'user' => 'USER_ID']);

        $team = new \Dvsa\Olcs\Api\Entity\User\Team();
        $this->mockUser->setTeam($team);

        $this->expectException(
            \Dvsa\Olcs\Api\Domain\Exception\Exception::class,
            'Cannot find printer for User LOGIN_ID'
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandStubLicence()
    {
        $command = Cmd::create(['id' => 'QUEUE_ID', 'documents' => ['DOC_ID'], 'title' => 'JOB', 'user' => 'USER_ID']);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(\Dvsa\Olcs\Api\Entity\System\SystemParameter::SELFSERVE_USER_PRINTER)->once()
            ->andReturn('TESTING-STUB-LICENCE:34');

        $this->repoMap['Document']->shouldReceive('save')->once()->andReturnUsing(
            function ($document) {
                $this->assertSame('PRINT DESC', $document->getDescription());
                $this->assertSame(34, $document->getLicence()->getId());
            }
        );

        $this->mockedSmServices['FileUploader']->shouldReceive('download')->never();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Printed successfully (stub to licence 34)"], $result->getMessages());
    }

    public function testHandleCommandStubLicenceMultiDocs()
    {
        $command = Cmd::create(
            ['id' => 'QUEUE_ID', 'documents' => ['DOC_ID', 'DOC2_ID'], 'title' => 'JOB', 'user' => 'USER_ID']
        );

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(\Dvsa\Olcs\Api\Entity\System\SystemParameter::SELFSERVE_USER_PRINTER)->once()
            ->andReturn('TESTING-STUB-LICENCE:34');

        $this->repoMap['Document']->shouldReceive('save')->once()->andReturnUsing(
            function ($document) {
                $this->assertSame('PRINT DESC', $document->getDescription());
                $this->assertSame(34, $document->getLicence()->getId());
            }
        );
        $this->repoMap['Document']->shouldReceive('save')->once()->andReturnUsing(
            function ($document) {
                $this->assertSame('PRINT DESC2', $document->getDescription());
                $this->assertSame(34, $document->getLicence()->getId());
            }
        );

        $this->mockedSmServices['FileUploader']->shouldReceive('download')->never();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Printed successfully (stub to licence 34)"], $result->getMessages());
    }

    private function expectPrintFile(
        $commandPdfResult = 0,
        $fileExists = true,
        $commandLprResult = 0,
        $userName = 'PRINT_USER',
        $copies = 1
    ) {
        $this->sut->shouldReceive('executeCommand')
            ->with("soffice --headless --convert-to pdf:writer_pdf_Export --outdir /tmp 'TEMP_FILE.rtf' 2>&1", [], null)
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

        $this->expectLpr($userName, $commandLprResult, $fileExists, $copies);
    }

    private function expectLpr($userName, $commandLprResult, $fileExists, $copies = 1)
    {
        $this->sut->shouldReceive('fileExists')->with('TEMP_FILE.pdf')->once()->andReturn($fileExists);
        if (!$fileExists) {
            return;
        }

        $this->sut->shouldReceive('executeCommand')
            ->with(
                "lpr 'TEMP_FILE.pdf'" .
                " -H 'PRINT_SERVER'" .
                " -C 'TEMP_FILE.pdf'" .
                " -h -P 'QUEUE1'" .
                " -U '{$userName}'".
                " -#{$copies}" .
                " -o collate=true" .
                " 2>&1",
                [],
                null
            )->once()
            ->andReturnUsing(
                function ($command, &$output, &$result) use ($commandLprResult) {
                    $result = $commandLprResult;
                    $output = ['OUTPUT LPR'];
                }
            );
    }
}
