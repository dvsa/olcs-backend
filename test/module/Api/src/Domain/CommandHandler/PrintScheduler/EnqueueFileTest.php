<?php

/**
 * Enqueue File Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\PrintScheduler;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler\EnqueueFile;
use Dvsa\Olcs\Api\Domain\Repository\Document as DocumentRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\EnqueueFile as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocument as CreateDocumentCmd;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Enqueue File Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class EnqueueFileTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new EnqueueFile();
        $this->mockRepo('Document', DocumentRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(['fileId' => 1, 'jobName' => 'jobName']);

        $data = [
            'identifier'    => 1,
            'description'   => 'jobName',
            'filename'      => 'jobName.rtf',
            'licence'       => 7,
            'category'      => 1,
            'subCategory'   => 91,
            'isExternal'    => false,
            'isReadOnly'    => true,
            'issuedDate'    => new DateTime('now'),
            'size'          => 1000
        ];

        $result1 = new Result();
        $result1->addId('document', 1);
        $result1->addMessage('Document created successfully');

        $this->expectedSideEffect(CreateDocumentCmd::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'document' => 1,
                'file' => 1
            ],
            'messages' => [
                'Document created successfully',
                'File printed'
            ]
        ];

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($expected, $result->toArray());
    }
}
