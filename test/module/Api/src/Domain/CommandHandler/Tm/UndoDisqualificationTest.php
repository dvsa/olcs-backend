<?php

/**
 * Undo disqualification test
 *
 * @author Alex Peshkov <alex.pehkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TransportManager;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Tm\UndoDisqualification;
use Dvsa\Olcs\Transfer\Command\Tm\UndoDisqualification as Cmd;
use Dvsa\Olcs\Api\Domain\Repository\TransportManager as TransportManagerRepo;
use \Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;

/**
 * Undo disqualification test
 *
 * @author Alex Peshkov <alex.pehkov@valtech.co.uk>
 */
class UndoDisqualificationTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new UndoDisqualification();
        $this->mockRepo('TransportManager', TransportManagerRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            TransportManagerEntity::TRANSPORT_MANAGER_STATUS_DISQUALIFIED,
            TransportManagerEntity::TRANSPORT_MANAGER_STATUS_CURRENT,
        ];
        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 1
        ];

        $command = Cmd::create($data);

        $this->repoMap['TransportManager']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->once()
            ->andReturn(
                m::mock()
                    ->shouldReceive('getTmStatus')
                    ->once()
                    ->andReturn(
                        m::mock()
                        ->shouldReceive('getId')
                        ->andReturn(TransportManagerEntity::TRANSPORT_MANAGER_STATUS_DISQUALIFIED)
                        ->once()
                        ->getMock()
                    )
                    ->shouldReceive('setDisqualificationTmCaseId')
                    ->with(null)
                    ->once()
                    ->shouldReceive('setTmStatus')
                    ->with($this->refData[TransportManagerEntity::TRANSPORT_MANAGER_STATUS_CURRENT])
                    ->once()
                    ->getMock()
            )
            ->shouldReceive('save')
            ->once();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => ['Disqualification removed']
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithCurrentStatus()
    {
        $data = [
            'id' => 1
        ];

        $command = Cmd::create($data);

        $this->repoMap['TransportManager']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->once()
            ->andReturn(
                m::mock()
                    ->shouldReceive('getTmStatus')
                    ->once()
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getId')
                            ->andReturn(TransportManagerEntity::TRANSPORT_MANAGER_STATUS_CURRENT)
                            ->once()
                            ->getMock()
                    )
                    ->getMock()
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => ['TM status is not disqualified']
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
