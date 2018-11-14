<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitWindow;

use Dvsa\Olcs\Api\Domain\Command\IrhpPermitWindow\Close as CloseCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitWindow\Close as CloseHandler;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as PermitWindowRepo;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as PermitWindowEntity;
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

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $windowId = 1;
        $app1Id = 10;
        $app2Id = 11;

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

        $app1 = m::mock(EcmtPermitApplicationEntity::class);
        $app1->shouldReceive('getId')
            ->andReturn($app1Id);

        $app2 = m::mock(EcmtPermitApplicationEntity::class);
        $app2->shouldReceive('getId')
            ->andReturn($app2Id);

        $this->repoMap['EcmtPermitApplication']
            ->shouldReceive('fetchByWindowId')
            ->with($windowId, [EcmtPermitApplicationEntity::STATUS_NOT_YET_SUBMITTED])
            ->andReturn([$app1, $app2]);

        $this->expectedSideEffect(
            CancelEcmtPermitApplication::class,
            [
                'id' => $app1Id,
            ],
            (new Result())->addMessage('App1 has been cancelled')
        );
        $this->expectedSideEffect(
            CancelEcmtPermitApplication::class,
            [
                'id' => $app2Id,
            ],
            (new Result())->addMessage('App2 has been cancelled')
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'id' => $windowId
            ],
            'messages' => [
                'App1 has been cancelled',
                'App2 has been cancelled',
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
