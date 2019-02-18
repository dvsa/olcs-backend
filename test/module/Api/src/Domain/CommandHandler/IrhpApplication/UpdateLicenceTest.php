<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\Command\IrhpPermitApplication\Delete;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\UpdateLicence as CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\UpdateLicence as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Identity\IdentityInterface;
use ZfcRbac\Service\AuthorizationService;

class UpdateLicenceTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);

        $authorizationService = m::mock(AuthorizationService::class)
            ->shouldReceive('getIdentity')
            ->andReturn(
                m::mock(IdentityInterface::class)
                    ->shouldReceive('getUser')
                    ->andReturn(
                        m::mock(User::class)
                            ->shouldReceive('getOrganisationUsers')
                            ->andReturn(
                                m::mock(OrganisationUser::class)
                                    ->shouldReceive('isEmpty')
                                    ->andReturn(false)
                                    ->getMock()
                            )
                            ->shouldReceive('getRelatedOrganisation')
                            ->andReturn(
                                m::mock('Organisation')
                                    ->shouldReceive('getId')
                                    ->andReturn(1)
                                    ->getMock()
                            )
                            ->getMock()
                    )
                    ->getMock()
            )
            ->getMock();

        $this->mockedSmServices = [
            AuthorizationService::class => $authorizationService
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 1,
            'licence' => 2
        ];

        $command = Command::create($data);

        $irhpPermitApplications = [
            1 => m::mock(IrhpPermitApplication::class)
                ->shouldReceive('getId')
                ->andReturn(1)
                ->getMock(),
            2 => m::mock(IrhpPermitApplication::class)
                ->shouldReceive('getId')
                ->andReturn(2)
                ->getMock()
        ];

        $fees = [
            1 => m::mock(Fee::class)
                ->shouldReceive('getId')
                ->andReturn(1)
                ->getMock(),
            2 => m::mock(Fee::class)
                ->shouldReceive('getId')
                ->andReturn(2)
                ->getMock()
        ];

        $this->commandHandler
            ->shouldReceive('handleCommand')
            ->with(m::type(Delete::class), false)
            ->andReturn(new Result())
            ->shouldReceive('handleCommand')
            ->with(m::type(CancelFee::class), false)
            ->andReturn(new Result());

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class);

        $licence
            ->shouldReceive('getRelatedOrganisation')
            ->once()
            ->andReturn(
                m::mock('Organisation')
                    ->shouldReceive('getId')
                    ->andReturn(1)
                    ->getMock()
            )
            ->shouldReceive('canMakeIrhpApplication')
            ->andReturn(true);

        /** @var IrhpApplicationEntity $irhpApplication */
        $irhpApplication = m::mock(IrhpApplicationEntity::class);

        $irhpApplication
            ->shouldReceive('getIrhpPermitType')
            ->andReturn(
                m::mock(IrhpPermitType::class)
            )
            ->getMock()
            ->shouldReceive('getIrhpPermitApplications')
            ->andReturn($irhpPermitApplications)
            ->shouldReceive('updateLicence')
            ->with($licence)
            ->shouldReceive('getOutstandingFees')
            ->andReturn($fees)
            ->shouldReceive('getId')
            ->andReturn(1);

        $this->repoMap['IrhpApplication']
            ->shouldReceive('fetchById')
            ->with(1)
            ->andReturn($irhpApplication)
            ->shouldReceive('save')
            ->with($irhpApplication);

        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->with(2)
            ->andReturn($licence);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'irhpApplication' => 1
            ],
            'messages' => [
                0 => 'IrhpApplication Licence Updated successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
