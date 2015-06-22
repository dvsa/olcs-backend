<?php

/**
 * SendTmApplicationTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendTmApplication as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Email\SendTmApplication as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * SendTmApplicationTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class SendTmApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();

        parent::setUp();
    }

    protected function initReferences()
    {
        parent::initReferences();
    }

    public function testHandleCommandAlreadyTmLoggedInUser()
    {
        $command = Command::create(['id' => 863]);

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['SendTmApplication needs to be implemented.'], $result->getMessages());
    }
}
