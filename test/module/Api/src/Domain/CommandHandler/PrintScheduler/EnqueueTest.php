<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\PrintScheduler;

use Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler\Enqueue as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as Cmd;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * CreateSeparatorSheetTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class EnqueueTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Document', \Dvsa\Olcs\Api\Domain\Repository\Document::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $this->markTestSkipped('This is a temporary implementation of Enqeue.');
    }
}
