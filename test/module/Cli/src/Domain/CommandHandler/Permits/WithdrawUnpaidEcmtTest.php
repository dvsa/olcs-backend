<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\Permits;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\Permits\WithdrawEcmtPermitApplication;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
use Dvsa\Olcs\Cli\Domain\Command\Permits\WithdrawUnpaidEcmt as WithdrawUnpaidEcmtCommand;
use Dvsa\Olcs\Cli\Domain\CommandHandler\Permits\WithdrawUnpaidEcmt as WithdrawUnpaidEcmtHandler;
use Dvsa\Olcs\Transfer\Query\Permits\EcmtPermitApplication as EcmtAppQuery;

use Mockery as m;

/**
 * Test building the list of unpaid apps to withdraw
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class WithdrawUnpaidEcmtTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new WithdrawUnpaidEcmtHandler();
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplicationRepo::class);

        parent::setUp();
    }

    /**
     * test handleCommand
     */
    public function testHandleCommand()
    {
        $appId1 = 111;
        $appId3 = 333;

        $ecmtApp1 = m::mock(EcmtPermitApplicationEntity::class);
        $ecmtApp1->shouldReceive('getId')->once()->withNoArgs()->andReturn($appId1);
        $ecmtApp1->shouldReceive('issueFeeOverdue')->once()->withNoArgs()->andReturn(true);

        $ecmtApp2 = m::mock(EcmtPermitApplicationEntity::class);
        $ecmtApp2->shouldReceive('getId')->never();
        $ecmtApp2->shouldReceive('issueFeeOverdue')->once()->withNoArgs()->andReturn(false);

        $ecmtApp3 = m::mock(EcmtPermitApplicationEntity::class);
        $ecmtApp3->shouldReceive('getId')->once()->withNoArgs()->andReturn($appId3);
        $ecmtApp3->shouldReceive('issueFeeOverdue')->once()->withNoArgs()->andReturn(true);

        $ecmtApps = [$ecmtApp1, $ecmtApp2, $ecmtApp3];

        $this->repoMap['EcmtPermitApplication']
            ->shouldReceive('fetchList')
            ->once()
            ->with(m::type(EcmtAppQuery::class), Query::HYDRATE_OBJECT)
            ->andReturn($ecmtApps);

        $this->withdrawSideEffect($appId1);
        $this->withdrawSideEffect($appId3);

        $cmd = WithdrawUnpaidEcmtCommand::create([]);
        $this->sut->handleCommand($cmd);

        $ecmtAppQuery = $this->sut->getQuery()->getArrayCopy();
        self::assertEquals([EcmtPermitApplication::STATUS_AWAITING_FEE], $ecmtAppQuery['statusIds']);
        self::assertNull($ecmtAppQuery['status']);
        self::assertNull($ecmtAppQuery['organisation']);
    }

    private function withdrawSideEffect($appId)
    {
        $this->expectedSideEffect(
            WithdrawEcmtPermitApplication::class,
            [
                'id' => $appId,
                'reason' => EcmtPermitApplicationEntity::WITHDRAWN_REASON_UNPAID
            ],
            new Result()
        );
    }
}
