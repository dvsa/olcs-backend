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

use Common\Rbac\User;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\UpdateEcmtLicence;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Transfer\Command\Permits\UpdateEcmtLicence as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Identity\IdentityInterface;
use ZfcRbac\Service\AuthorizationService;
use Mockery as m;

class UpdateEcmtLicenceTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateEcmtLicence();
        $this->mockRepo('EcmtPermitApplication', Repository\EcmtPermitApplication::class);
        $this->mockRepo('IrhpPermitApplication', Repository\IrhpPermitApplication::class);
        $this->mockRepo('Licence', Repository\Licence::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)->shouldReceive('getIdentity')
                ->andReturn(
                    m::mock(IdentityInterface::class)->shouldReceive('getUser')
                        ->andReturn(
                            m::mock(User::class)->shouldReceive('getOrganisationUsers')->once()
                                ->andReturn(
                                    m::mock(OrganisationUser::class)
                                        ->shouldReceive('isEmpty')->andReturn(false)
                                        ->getMock()
                                )
                                ->shouldReceive("getRelatedOrganisation")
                                ->once()
                                ->andReturn(
                                    m::mock('Organisation')
                                        ->shouldReceive('getId')
                                        ->andReturn(1)
                                        ->getMock()
                                )
                                ->getMock()
                        )->getMock()
                )
                ->getMock()
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = $this->createCommand();
        $licence = m::mock(Licence::class);

        $licence->shouldReceive('getRelatedOrganisation')->andReturn(
            m::mock(Organisation::class)->shouldReceive(
                'getId'
            )->andReturn(1)
                ->getMock()
        );
        $licence->shouldReceive('canMakeEcmtApplication')->andReturn(true);

        $application = m::mock(EcmtPermitApplication::class);

        $application->shouldReceive('updateLicence')->with($licence)->once();
        $application->shouldReceive('getFees')->once()->andReturn([]);
        $application->shouldReceive('getId')->withNoArgs()->once()->andReturn(5);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);

        $application->shouldReceive('getFirstIrhpPermitApplication')->withNoArgs()->once()->andReturn($irhpPermitApplication);

        $irhpPermitApplication->shouldReceive('updateLicence')
            ->once()
            ->globally()
            ->ordered()
            ->with($licence);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with(5)
            ->andReturn($application);

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(7)
            ->andReturn($licence);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('save')
            ->once()
            ->with($application);

        $this->repoMap['IrhpPermitApplication']->shouldReceive('save')
            ->globally()
            ->ordered()
            ->once()
            ->with($irhpPermitApplication);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['ecmtPermitApplication' => 5],
            'messages' => [
                'EcmtPermitApplication Licence Updated successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testExpectedExceptionWhenLicenceOrgDoesNotMatchUser()
    {

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('Licence does not belong to this organisation');


        $command = $this->createCommand();
        $licence = m::mock(Licence::class);

        $licence->shouldReceive('getRelatedOrganisation')->andReturn(
            m::mock(Organisation::class)->shouldReceive(
                'getId'
            )->andReturn(2)
                ->getMock()
        );

        $licence->shouldReceive('canMakeEcmtApplication')->andReturn(true);

        $application = m::mock(EcmtPermitApplication::class);


        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with(5)
            ->andReturn($application);

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(7)
            ->andReturn($licence);
            $this->sut->handleCommand($command);
    }

    public function testExpectedExceptionWhenCanMakeEcmtApplicationFalse()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('Licence ID 1 with number 1 is unable to make an ECMT application');

        $command = $this->createCommand();
        $licence = m::mock(Licence::class);

        $licence->shouldReceive('getRelatedOrganisation')->andReturn(
            m::mock(Organisation::class)->shouldReceive(
                'getId'
            )->andReturn(1)
                ->getMock()
        );
        $licence->shouldReceive('getId')->andReturn(1);
        $licence->shouldReceive('getLicNo')->andReturn(1);
        $licence->shouldReceive('canMakeEcmtApplication')->andReturn(false);

        $application = m::mock(EcmtPermitApplication::class);


        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with(5)
            ->andReturn($application);

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(7)
            ->andReturn($licence);

        $this->sut->handleCommand($command);
    }

    /**
     * createCommand
     *
     * @return Cmd
     */
    private function createCommand()
    {
        $data = [
            'id' => 5,
            'licence' => 7
        ];

        $command = Cmd::create($data);
        return $command;
    }
}
