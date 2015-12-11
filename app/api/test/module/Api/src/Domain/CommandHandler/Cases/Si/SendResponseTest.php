<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Si;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * SendResponseTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class SendResponseTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new \Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Si\SendResponse();

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = m::mock(\Dvsa\Olcs\Transfer\Command\AbstractCommand::class);

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }
}
