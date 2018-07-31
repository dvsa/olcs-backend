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
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\Permits\UpdateEcmtEmissions as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Doctrine\ORM\Query;
use Mockery as m;

class UpdateEcmtEmissionsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateEcmtEmissions();
        $this->mockRepo('EcmtPermitApplication', Repository\EcmtPermitApplication::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 4,
            'emissions' => 0
        ];

        $command = Cmd::create($data);

        $application = m::mock(EcmtPermitApplication::class);
        $application->shouldReceive('getId')->withNoArgs()->once()->andReturn(4);
        $application->shouldReceive('setEmissions')
            ->once()
            ->with(0);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['ecmtEuro6'=>4],
            'messages' => [
                'ECMT Permit Application Euro6 updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
