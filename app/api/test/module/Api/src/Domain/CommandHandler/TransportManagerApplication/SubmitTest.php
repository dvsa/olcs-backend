<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication\Submit as CommandHandler;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Transfer\Command\TransportManagerApplication\UpdateStatus as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * SubmitTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class SubmitTest extends CommandHandlerTestCase
{
    protected $loggedInUser;

    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo(
            'TransportManagerApplication',
            \Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication::class
        );

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            TransportManagerApplication::STATUS_TM_SIGNED,
            TransportManagerApplication::STATUS_OPERATOR_SIGNED,
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithVersion()
    {
        $command = Command::create(['id' => 863, 'version' => 234]);

        $tma = new TransportManagerApplication();
        $tma->setIsOwner('N');

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchUsingId')->once()
            ->with($command, \Doctrine\ORM\Query::HYDRATE_OBJECT, 234)->andReturn($tma);
        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once()->andReturnUsing(
            function (TransportManagerApplication $tma) {
                $this->assertSame(
                    $this->refData[TransportManagerApplication::STATUS_TM_SIGNED],
                    $tma->getTmApplicationStatus()
                );
            }
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandIsOwnerY()
    {
        $command = Command::create(['id' => 863]);

        $tma = new TransportManagerApplication();
        $tma->setIsOwner('Y');

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchUsingId')->once()
            ->with($command)->andReturn($tma);
        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once()->andReturnUsing(
            function (TransportManagerApplication $tma) {
                $this->assertSame(
                    $this->refData[TransportManagerApplication::STATUS_OPERATOR_SIGNED],
                    $tma->getTmApplicationStatus()
                );
            }
        );

        $this->sut->handleCommand($command);
    }
}
