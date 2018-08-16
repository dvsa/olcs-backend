<?php
/**
 * Created by IntelliJ IDEA.
 * Date: 26/07/2018
 * Time: 12:02
 */

/**
 * Update ECMT EURO6 Emissions Test
 *
 * @author ONE
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\WithdrawEcmtPermitApplication;

use Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\Permits\WithdrawEcmtPermitApplication as Cmd;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Doctrine\ORM\Query;
use Mockery as m;

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
        $application->shouldReceive('getId')->withNoArgs()->once()->andReturn(1);

        $application->shouldReceive('setStatus')->with($this->refData[EcmtPermitApplication::STATUS_WITHDRAWN])->once();

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
            'messages' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
