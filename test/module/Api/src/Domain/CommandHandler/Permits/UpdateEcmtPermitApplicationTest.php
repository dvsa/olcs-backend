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

use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\UpdateEcmtEmissions;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\UpdateEcmtPermitApplication;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\Sectors;
use Dvsa\Olcs\Transfer\Command\Permits\UpdateEcmtPermitApplication as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Doctrine\ORM\Query;
use Mockery as m;

class UpdateEcmtPermitApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateEcmtPermitApplication();
        $this->mockRepo('EcmtPermitApplication', Repository\EcmtPermitApplication::class);
        $this->mockRepo('Sectors', Repository\Licence::class);
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 4,
            'emissions' => 1,
            'cabotage' => 1,
            'sectors' => 7
        ];

        $command = Cmd::create($data);
        $sectors = m::mock(Sectors::class);

        $application = m::mock(EcmtPermitApplication::class);
        $application->shouldReceive('getId')->withNoArgs()->once()->andReturn(4);
        $application->shouldReceive('setSectors')
            ->once();
        $application->shouldReceive('setDeclaration')
            ->once();
        $application->shouldReceive('setPermitsRequired')
            ->once();
        $application->shouldReceive('setTrips')
            ->once();
        $application->shouldReceive('setInternationalJourneys')
            ->once();
        $application->shouldReceive('setDateReceived')
            ->once();
        $application->shouldReceive('setEmissions')
            ->once()
            ->with(1);
        $application->shouldReceive('setCabotage')
            ->once()
            ->with(1);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $this->repoMap['Sectors']->shouldReceive('fetchById')
            ->with(7)
            ->andReturn($sectors);





        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['ecmtPermitApplication'=>4],
            'messages' => [
                'ECMT Permit Application updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
