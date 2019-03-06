<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermit;

use Dvsa\Olcs\Api\Domain\Command\Permits\ExpireEcmtPermitApplication;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermit\Terminate as CmdHandler;
use Dvsa\Olcs\Transfer\Command\IrhpPermit\Terminate;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
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

    /**
     * Tests exception thrown for not being an ECMT permit
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testPermitTypeException()
    {
        $cmdData = ['id' => '9'];
        $command = Terminate::create($cmdData);

        $permit = m::mock(IrhpPermit::class)->makePartial();
        $this->repoMap['IrhpPermit']
            ->shouldReceive('fetchById')
            ->with($cmdData['id'])
            ->andReturn($permit);

        $permit->shouldReceive('getIrhpPermitRange->getIrhpPermitStock->getIrhpPermitType->isEcmtAnnual')
            ->andReturn(false);

        $this->sut->handleCommand($command);
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
        $permit->shouldReceive('getIrhpPermitRange->getIrhpPermitStock->getIrhpPermitType->isEcmtAnnual')
            ->andReturn(true);
        $terminatedStatus = new RefData(IrhpPermit::STATUS_CEASED);
        $permit->setStatus($terminatedStatus);
        $permit->shouldReceive('proceedToStatus')->with($terminatedStatus);
        $result = $this->sut->handleCommand($command);
        $expected = [
            'messages' => ['You cannot terminate an inactive permit.'],
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

        $permit->shouldReceive('getIrhpPermitRange->getIrhpPermitStock->getIrhpPermitType->isEcmtAnnual')
            ->andReturn(true);

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
        $permit->shouldReceive('getIrhpPermitApplication->hasValidPermits')
            ->andReturn(true);

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

        $permit->shouldReceive('getIrhpPermitRange->getIrhpPermitStock->getIrhpPermitType->isEcmtAnnual')
            ->andReturn(true);
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
        $permit->shouldReceive('getIrhpPermitApplication->hasValidPermits')
            ->andReturn(false);

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
