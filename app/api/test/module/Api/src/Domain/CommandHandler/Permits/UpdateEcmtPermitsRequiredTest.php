<?php
/**
 * Created by IntelliJ IDEA.
 * Date: 26/07/2018
 * Time: 12:02
 */

/**
 * Update ECMT EURO6 Permits Required Test
 *
 * @author ONE
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\UpdateEcmtPermitsRequired;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\Permits\UpdateEcmtPermitsRequired as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Doctrine\ORM\Query;
use Mockery as m;

class UpdateEcmtPermitsRequiredTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateEcmtPermitsRequired();
        $this->mockRepo('EcmtPermitApplication', Repository\EcmtPermitApplication::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 4,
            'requiredEuro5' => 2,
            'requiredEuro6' => 1
        ];

        $command = Cmd::create($data);

        $application = m::mock(EcmtPermitApplication::class);
        $licence = m::mock(Licence::class);
        $application->shouldReceive('calculateTotalPermitsRequired')->withNoArgs()->once()->andReturn(3);
        $application->shouldReceive('getLicence')->withNoArgs()->once()->andReturn($licence);
        $application->shouldReceive('getId')->withNoArgs()->once()->andReturn(4);
        $application->shouldReceive('updatePermitsRequired')
            ->once()
            ->with(2, 1)
            ->ordered()
            ->globally();

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application)
            ->ordered()
            ->globally();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['ecmtPermitsRequired'=>4],
            'messages' => [
                'ECMT Permit Application Permits Required updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
