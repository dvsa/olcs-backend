<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Organisation;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Organisation\FixIsIrfo;
use Dvsa\Olcs\Api\Domain\Command\Organisation\FixIsIrfo as Cmd;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;

/**
 * Class FixIsIrfoTest
 */
class FixIsIrfoTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new FixIsIrfo();
        $this->mockRepo('Organisation', OrganisationRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create([]);

        $this->repoMap['Organisation']->shouldReceive('fixIsIrfo')->with()->once()->andReturn(45);

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['45 organisation(s) changed to isIrfo = 0'], $result->getMessages());
    }
}
