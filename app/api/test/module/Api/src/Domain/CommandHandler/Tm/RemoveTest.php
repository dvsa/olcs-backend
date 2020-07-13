<?php

/**
 * RemoveTest.php
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TransportManager;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Tm\Remove;
use Dvsa\Olcs\Transfer\Command\Tm\Remove as Cmd;
use Dvsa\Olcs\Api\Domain\Repository\TransportManager as TransportManagerRepo;
use Dvsa\Olcs\Api\Domain\Command\Result;
use \Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;

/**
 * Class UpdateTest
 *
 * @package Dvsa\OlcsTest\Api\Domain\CommandHandler\TransportManager
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class RemoveTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new Remove();
        $this->mockRepo('TransportManager', TransportManagerRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            TransportManagerEntity::TRANSPORT_MANAGER_STATUS_REMOVED,
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
            ->shouldReceive('fetchById')
            ->with(1)
            ->andReturn(
                m::mock()
                    ->shouldReceive('setRemovedDate')
                    ->once()
                    ->shouldReceive('setTmStatus')
                    ->with($this->refData[TransportManagerEntity::TRANSPORT_MANAGER_STATUS_REMOVED])
                    ->once()
                    ->getMock()
            )->shouldReceive('save');

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => ['Removed transport manager.']
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
