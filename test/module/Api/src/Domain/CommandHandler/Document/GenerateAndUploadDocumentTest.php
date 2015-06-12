<?php

/**
 * Generate and Upload Document Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Document\GenerateAndUploadDocument;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndUploadDocument as Cmd;

/**
 * Create Other Licence Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GenerateAndUploadDocumentTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new GenerateAndUploadDocument();
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create([]);
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['fileId' => 1],
            'messages' => ['Document generated and uploaded']
        ];

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($expected, $result->toArray());
    }
}
