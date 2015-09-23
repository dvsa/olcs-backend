<?php

/**
 * UpdateStatusTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication\UpdateStatus as CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication;
use Dvsa\Olcs\Transfer\Command\TransportManagerApplication\UpdateStatus as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * UpdateStatusTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UpdateStatusTest extends CommandHandlerTestCase
{
    protected $loggedInUser;

    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('TransportManagerApplication', TransportManagerApplication::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = ['status1'];

        parent::initReferences();
    }

    public function testHandleCommandWithVersion()
    {
        $command = Command::create(['id' => 863, 'version' => 234, 'status' => 'status1']);

        $tma = new \Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication();

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchUsingId')->once()
            ->with($command, \Doctrine\ORM\Query::HYDRATE_OBJECT, 234)->andReturn($tma);
        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication $tma) {
                $this->assertSame($this->refData['status1'], $tma->getTmApplicationStatus());
            }
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWithoutVersion()
    {
        $command = Command::create(['id' => 863, 'status' => 'status1']);

        $tma = new \Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication();

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchUsingId')->once()
            ->with($command)->andReturn($tma);
        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication $tma) {
                $this->assertSame($this->refData['status1'], $tma->getTmApplicationStatus());
            }
        );

        $this->sut->handleCommand($command);
    }
}
