<?php

/**
 * Print discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Discs;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as EnqueueFileCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler\Discs\PrintDiscs;
use Dvsa\Olcs\Api\Service\Document\DocumentGenerator as DocGenerator;
use Dvsa\Olcs\Api\Domain\Command\Discs\PrintDiscs as Cmd;
use Dvsa\Olcs\Transfer\Query\Document\DocumentList as Qry;

/**
 * Print discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PrintDiscsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PrintDiscs();

        $this->mockedSmServices = [
            'DocumentGenerator' => m::mock(DocGenerator::class)
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $mockDisc = m::mock()
            ->shouldReceive('getId')
            ->andReturn(1)
            ->once()
            ->getMock();

        $template = 'GVDiscTemplate';
        $data = [
            'type' => 'Goods',
            'discs' => [$mockDisc],
            'startNumber' => 1
        ];
        $command = Cmd::create($data);

        $mockStoredFile = m::mock()
            ->shouldReceive('getIdentifier')
            ->andReturn('id')
            ->once()
            ->getMock();

        $this->mockedSmServices['DocumentGenerator']
            ->shouldReceive('generateFromTemplate')
            ->with($template, [0 => 1], ['Disc_List' => [['discNo' => '1']]])
            ->andReturn('document')
            ->shouldReceive('uploadGeneratedContent')
            ->with('document', 'documents', 'GVDiscTemplate.rtf')
            ->andReturn($mockStoredFile)
            ->once()
            ->getMock();

        $printQueueData = [
            'fileIdentifier' => 'id',
            'jobName' => 'Goods Disc List'
        ];
        $this->expectedSideEffect(EnqueueFileCommand::class, $printQueueData, new Result());

        $expected = [
            'id' => [],
            'messages' => ['Discs printed']
        ];

        $result = $this->sut->handleCommand($command);
        $this->assertEquals($expected, $result->toArray());
    }
}
