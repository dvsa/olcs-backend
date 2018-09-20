<?php

/**
 * Withdraw ECMT Permit Application Test
 *
 * @author Scott Callaway
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\WithdrawEcmtPermitApplication;

use Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\Permits\WithdrawEcmtPermitApplication as Cmd;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Class WithdrawEcmtApplicationTest
 */
class WithdrawEcmtApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new WithdrawEcmtPermitApplication();
        $this->mockRepo('EcmtPermitApplication', Repository\EcmtPermitApplication::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
          EcmtPermitApplication::STATUS_WITHDRAWN
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $applicationId = 1;
        $command = Cmd::create(['id' => $applicationId]);

        $application = m::mock(EcmtPermitApplication::class);
        $application->shouldReceive('withdraw')->with($this->refData[EcmtPermitApplication::STATUS_WITHDRAWN])->once();

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with($applicationId)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
              'ecmtPermitApplication' => $applicationId
            ],
            'messages' => ['Permit application withdrawn']
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
