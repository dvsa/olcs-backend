<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Organisation;

use Dvsa\Olcs\Api\Domain\CommandHandler\Organisation\FixIsUnlicenced;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Organisation\FixIsIrfo as Cmd;

/**
 * Class FixIsUnlicencedTest
 */
class FixIsUnlicencedTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new FixIsUnlicenced();
        $this->mockRepo('Organisation', Organisation::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create([]);

        $this->repoMap['Organisation']->shouldReceive('fixIsUnlicenced')->with()->once()->andReturn(45);

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['45 organisation(s) changed to isUnlicenced = 0'], $result->getMessages());
    }
}
