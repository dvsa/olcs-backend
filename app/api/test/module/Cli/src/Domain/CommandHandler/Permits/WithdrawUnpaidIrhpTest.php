<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\WithdrawableInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\Withdraw as WithdrawCmd;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Cli\Domain\Command\Permits\WithdrawUnpaidIrhp as WithdrawUnpaidIrhpCmd;
use Dvsa\Olcs\Cli\Domain\CommandHandler\Permits\WithdrawUnpaidIrhp as WithdrawUnpaidIrhpHandler;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Mockery as m;

/**
 * Test building the list of unpaid irhp apps to withdraw
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class WithdrawUnpaidIrhpTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new WithdrawUnpaidIrhpHandler();
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $irhpApplication1Id = 455;
        $irhpApplication1 = m::mock(IrhpApplication::class);
        $irhpApplication1->shouldReceive('getId')
            ->andReturn($irhpApplication1Id);
        $irhpApplication1->shouldReceive('issueFeeOverdue')
            ->andReturn(false);

        $irhpApplication2Id = 458;
        $irhpApplication2 = m::mock(IrhpApplication::class);
        $irhpApplication2->shouldReceive('getId')
            ->andReturn($irhpApplication2Id);
        $irhpApplication2->shouldReceive('issueFeeOverdue')
            ->andReturn(true);

        $irhpApplication3Id = 467;
        $irhpApplication3 = m::mock(IrhpApplication::class);
        $irhpApplication3->shouldReceive('getId')
            ->andReturn($irhpApplication3Id);
        $irhpApplication3->shouldReceive('issueFeeOverdue')
            ->andReturn(false);

        $irhpApplication4Id = 471;
        $irhpApplication4 = m::mock(IrhpApplication::class);
        $irhpApplication4->shouldReceive('getId')
            ->andReturn($irhpApplication4Id);
        $irhpApplication4->shouldReceive('issueFeeOverdue')
            ->andReturn(true);

        $irhpApplications = [
            $irhpApplication1,
            $irhpApplication2,
            $irhpApplication3,
            $irhpApplication4,
        ];

        $this->repoMap['IrhpApplication']->shouldReceive('fetchAllAwaitingFee')
            ->withNoArgs()
            ->andReturn($irhpApplications);

        $this->expectedSideEffect(
            WithdrawCmd::class,
            [
                'id' => $irhpApplication2Id,
                'reason' => WithdrawableInterface::WITHDRAWN_REASON_UNPAID,
            ],
            new Result()
        );

        $this->expectedSideEffect(
            WithdrawCmd::class,
            [
                'id' => $irhpApplication4Id,
                'reason' => WithdrawableInterface::WITHDRAWN_REASON_UNPAID,
            ],
            new Result()
        );

        $cmd = WithdrawUnpaidIrhpCmd::create([]);
        $this->sut->handleCommand($cmd);
    }
}
