<?php
/**
 * Created by IntelliJ IDEA.
 * Date: 26/07/2018
 * Time: 12:02
 */

/**
 * Update ECMT Licence Test
 *
 * @author ONE
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\UpdateEcmtLicence;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\Permits\UpdateEcmtLicence as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;
use Mockery as m;

class UpdateEcmtLicenceTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateEcmtLicence();
        $this->mockRepo('EcmtPermitApplication', Repository\EcmtPermitApplication::class);
        $this->mockRepo('Licence', Repository\Licence::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $this->markTestSkipped();
        $data = [
            'id' => 5,
            'licence' => 7
        ];

        $command = Cmd::create($data);
        $licence = m::mock(Licence::class);
        $application = m::mock(EcmtPermitApplication::class);

        $application->shouldReceive('updateLicence')->with($licence)->once();
        $application->shouldReceive('getFees')->once()->andReturn([]);
        $application->shouldreceive('getId')->withNoArgs()->once()->andReturn(5);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with(5)
            ->andReturn($application);

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(7)
            ->andReturn($licence);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('save')
            ->once()
            ->with($application);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['ecmtPermitApplication'=> 5],
            'messages' => [
                'EcmtPermitApplication Licence Updated successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
