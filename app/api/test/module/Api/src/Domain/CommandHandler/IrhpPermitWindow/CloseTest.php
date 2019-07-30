<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitWindow;

use Dvsa\Olcs\Api\Domain\Command\IrhpPermitWindow\Close as CloseCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitWindow\Close as CloseHandler;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as PermitWindowRepo;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as PermitWindowEntity;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\CancelApplication as CancelIrhpApplication;
use Dvsa\Olcs\Transfer\Command\Permits\CancelEcmtPermitApplication;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Close IRHP Permit Window Test
 */
class CloseTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CloseHandler();
        $this->mockRepo('IrhpPermitWindow', PermitWindowRepo::class);
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplicationRepo::class);
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $windowId = 1;
        $ecmtApp1Id = 10;
        $ecmtApp2Id = 11;
        $irhpApp1Id = 14;
        $irhpApp2Id = 15;

        $cmdData = [
            'id' => $windowId,
        ];

        $command = CloseCmd::create($cmdData);

        $window = m::mock(PermitWindowEntity::class);
        $window->shouldReceive('hasEnded')
            ->andReturn(true);
        $window->shouldReceive('getId')
            ->andReturn($windowId);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchById')
            ->with($cmdData['id'])
            ->andReturn($window);

        $ecmtApp1 = m::mock(EcmtPermitApplicationEntity::class);
        $ecmtApp1->shouldReceive('getId')
            ->andReturn($ecmtApp1Id);

        $ecmtApp2 = m::mock(EcmtPermitApplicationEntity::class);
        $ecmtApp2->shouldReceive('getId')
            ->andReturn($ecmtApp2Id);

        $irhpApp1 = m::mock(IrhpApplication::class);
        $irhpApp1->shouldReceive('getId')
            ->andReturn($irhpApp1Id);

        $irhpApp2 = m::mock(IrhpApplication::class);
        $irhpApp2->shouldReceive('getId')
            ->andReturn($irhpApp2Id);

        $this->repoMap['EcmtPermitApplication']
            ->shouldReceive('fetchByWindowId')
            ->with($windowId, [EcmtPermitApplicationEntity::STATUS_NOT_YET_SUBMITTED])
            ->andReturn([$ecmtApp1, $ecmtApp2]);

        $this->repoMap['IrhpApplication']
            ->shouldReceive('fetchByWindowId')
            ->with($windowId, [IrhpInterface::STATUS_NOT_YET_SUBMITTED])
            ->andReturn([$irhpApp1, $irhpApp2]);

        $this->expectedSideEffect(
            CancelEcmtPermitApplication::class,
            [
                'id' => $ecmtApp1Id,
            ],
            (new Result())->addMessage('ECMT App1 has been cancelled')
        );

        $this->expectedSideEffect(
            CancelEcmtPermitApplication::class,
            [
                'id' => $ecmtApp2Id,
            ],
            (new Result())->addMessage('ECMT App2 has been cancelled')
        );

        $this->expectedSideEffect(
            CancelIrhpApplication::class,
            [
                'id' => $irhpApp1Id,
            ],
            (new Result())->addMessage('IRHP App1 has been cancelled')
        );

        $this->expectedSideEffect(
            CancelIrhpApplication::class,
            [
                'id' => $irhpApp2Id,
            ],
            (new Result())->addMessage('IRHP App2 has been cancelled')
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'id' => $windowId
            ],
            'messages' => [
                'ECMT App1 has been cancelled',
                'ECMT App2 has been cancelled',
                'IRHP App1 has been cancelled',
                'IRHP App2 has been cancelled',
                sprintf('IRHP permit window \'%d\' has been closed', $windowId)
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     * @expectedExceptionMessage Window which has not ended cannot be closed
     */
    public function testHandleWindowOpen()
    {
        $cmdData = [
            'id' => 1,
        ];

        $command = CloseCmd::create($cmdData);

        $permitWindowEntity = m::mock(PermitWindowEntity::class);
        $permitWindowEntity
            ->shouldReceive('hasEnded')
            ->andReturn(false);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchById')
            ->with($cmdData['id'])
            ->andReturn($permitWindowEntity);

        $this->sut->handleCommand($command);
    }
}
