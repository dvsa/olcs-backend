<?php

/**
 * DeleteTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Fee;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication\Delete as CommandHandler;
use Dvsa\Olcs\Transfer\Command\TransportManagerApplication\Delete as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication;

/**
 * CreateTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class DeleteTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('TransportManagerApplication', TransportManagerApplication::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Command::create(['ids' => [863, 234]]);

        $tma1 = new \Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication();
        $tma2 = new \Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication();

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchById')->with(863)->once()->andReturn($tma1);
        $this->repoMap['TransportManagerApplication']->shouldReceive('delete')->with($tma1)->once();
        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchById')->with(234)->once()->andReturn($tma2);
        $this->repoMap['TransportManagerApplication']->shouldReceive('delete')->with($tma2)->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(
            [
                'Transport Manager Application ID 863 deleted',
                'Transport Manager Application ID 234 deleted'
            ],
            $result->getMessages()
        );
    }
}
