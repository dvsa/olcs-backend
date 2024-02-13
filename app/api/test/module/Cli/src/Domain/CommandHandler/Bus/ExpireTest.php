<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\Bus;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\Olcs\Cli\Domain\Command\Bus\Expire as ExpireBusCmd;
use Dvsa\Olcs\Cli\Domain\CommandHandler\Bus\Expire as ExpireHandler;
use Mockery as m;

/**
 * Bus reg expiry command handler test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ExpireTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new ExpireHandler();
        $this->mockRepo('Bus', BusRepo::class);

        parent::setUp();
    }

    /**
     * tests handleCommand
     */
    public function testHandleCommand()
    {
        $numExpired = 666;
        $this->repoMap['Bus']->expects('expireRegistrations')->withNoArgs()->andReturn($numExpired);
        $result = $this->sut->handleCommand(ExpireBusCmd::create([]));

        $messages = [0 => $numExpired . ' registrations have been expired'];
        $this->assertEquals($messages, $result->getMessages());
    }
}
