<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermit;

use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermit\Terminate as CmdHandler;
use Dvsa\Olcs\Transfer\Command\IrhpPermit\Terminate;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Permits\ExpireEcmtPermitApplication;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepo;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Terminate IRHP Permit Test
 *
 * @author Tonci Vidovic <Tonci.vidovic@capgemini.com>
 */
class TerminateTest extends CommandHandlerTestCase
{

    public function setUp()
    {
        $this->sut = m::mock(CmdHandler::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $this->mockRepo('IrhpPermit', IrhpPermitRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrhpPermit::STATUS_TERMINATED,
        ];

        parent::initReferences();
    }

    public function testCatchForbiddenException()
    {
        $cmdData = ['id' => '9'];

        $command = Terminate::create($cmdData);

        $permit = m::mock(IrhpPermit::class)->makePartial();
        $this->repoMap['IrhpPermit']
            ->shouldReceive('fetchById')
            ->with($cmdData['id'])
            ->andReturn($permit);

        $terminatedStatus = new RefData(IrhpPermit::STATUS_CEASED);
        $permit->setStatus($terminatedStatus);
        $permit->shouldReceive('proceedToStatus')->with($terminatedStatus);
        $result = $this->sut->handleCommand($command);
        $expected = [
            'messages' => ['The permit is not in the correct state to be terminated.'],
            'id' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testTerminatePermit()
    {
        $cmdData = ['id' => '9'];
        $command = Terminate::create($cmdData);

        $permit = m::mock(IrhpPermit::class)->makePartial();
        $this->repoMap['IrhpPermit']
            ->shouldReceive('fetchById')
            ->with($cmdData['id'])
            ->andReturn($permit);

        $terminatedStatus = new RefData(IrhpPermit::STATUS_PENDING);
        $permit->setStatus($terminatedStatus);
        $permit->shouldReceive('proceedToStatus')->with($terminatedStatus);

        $this->repoMap['IrhpPermit']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(IrhpPermit::class))
            ->andReturnUsing(
                function (IrhpPermit $irhpPermit) use (&$savedIrhpPermit) {
                    $irhpPermit->setId(9);
                    $savedIrhpPermit = $irhpPermit;
                }
            );
        $permit->shouldReceive('getIrhpPermitApplication->getEcmtPermitApplication->getId')
            ->andReturn(1);

        $this->sut->shouldReceive('handleQuery')
            ->once()
            ->andReturnUsing(function ($query) {
                return [
                    'count' => 1
                ];
            });

        $result = $this->sut->handleCommand($command);
        $expected = [
            'messages' => ['The selected permit has been terminated.'],
            'id' => ['IrhpPermit' => 9]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testTerminateLastPermit()
    {
        $cmdData = ['id' => '9'];

        $command = Terminate::create($cmdData);

        $permit = m::mock(IrhpPermit::class)->makePartial();
        $this->repoMap['IrhpPermit']
            ->shouldReceive('fetchById')
            ->with($cmdData['id'])
            ->andReturn($permit);

        $terminatedStatus = new RefData(IrhpPermit::STATUS_PENDING);
        $permit->setStatus($terminatedStatus);
        $permit->shouldReceive('proceedToStatus')->with($terminatedStatus);

        $this->repoMap['IrhpPermit']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(IrhpPermit::class))
            ->andReturnUsing(
                function (IrhpPermit $irhpPermit) use (&$savedIrhpPermit) {
                    $irhpPermit->setId(9);
                    $savedIrhpPermit = $irhpPermit;
                }
            );
        $permit->shouldReceive('getIrhpPermitApplication->getEcmtPermitApplication->getId')
            ->andReturn(1);

        $this->sut->shouldReceive('handleQuery')
            ->once()
            ->andReturnUsing(function ($query) {
                return [
                    'count' => 0
                ];
            });

        $sideEffectResult = new Result();
        $createCmdData = [
            'id' => 1
        ];
        $this->expectedSideEffect(ExpireEcmtPermitApplication::class, $createCmdData, $sideEffectResult);

        $result = $this->sut->handleCommand($command);
        $expected = [
            'messages' => ['The selected permit has been terminated.'],
            'id' => ['IrhpPermit' => 9]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
