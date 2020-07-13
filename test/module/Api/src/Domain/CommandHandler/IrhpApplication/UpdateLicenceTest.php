<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\Command\IrhpApplication\ResetIrhpPermitApplications;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\UpdateLicence;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\UpdateLicence as CommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\UpdateLicence as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Identity\IdentityInterface;
use ZfcRbac\Service\AuthorizationService;

class UpdateLicenceTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class),
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

        $orgId = 1245;
        $organisation = m::mock(OrganisationEntity::class);
        $organisation->shouldReceive('getId')->once()->withNoArgs()->andReturn($orgId);

        $licenceOrganisation = m::mock(OrganisationEntity::class);
        $licenceOrganisation->shouldReceive('getId')->once()->withNoArgs()->andReturn($orgId);

        $identity = m::mock(IdentityInterface::class);
        $identity->shouldReceive('getUser->getOrganisationUsers->isEmpty')->once()->andReturnFalse();
        $identity->shouldReceive('getUser->getRelatedOrganisation')->once()->andReturn($organisation);

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('getIdentity')
            ->once()
            ->andReturn($identity);

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
            ->with(m::type(CancelFee::class), false)
            ->andReturn(new Result());

        $irhpPermitStock = m::mock(IrhpPermitStock::class);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class);

        $licence
            ->shouldReceive('getRelatedOrganisation')
            ->once()
            ->withNoArgs()
            ->andReturn($licenceOrganisation);

        /** @var IrhpApplicationEntity $irhpApplication */
        $irhpApplication = m::mock(IrhpApplicationEntity::class);

        $irhpApplication->shouldReceive('getAssociatedStock')
            ->once()
            ->withNoArgs()
            ->andReturn($irhpPermitStock);
        $irhpApplication->shouldReceive('isMultiStock')
            ->once()
            ->withNoArgs()
            ->andReturnFalse();
        $irhpApplication->shouldReceive('updateLicence')
            ->once()
            ->with($licence);
        $irhpApplication->shouldReceive('getOutstandingFees')
            ->once()
            ->withNoArgs()
            ->andReturn($fees);
        $irhpApplication->shouldReceive('getId')
            ->once()
            ->withNoArgs()
            ->andReturn(1);

        $licence->shouldReceive('canMakeIrhpApplication')
            ->once()
            ->with($irhpPermitStock, $irhpApplication)
            ->andReturnTrue();

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

        $sideEffectResult = new Result();
        $sideEffectResult->addMessage('Message from ResetIrhpPermitApplications');

        $this->expectedSideEffect(
            ResetIrhpPermitApplications::class,
            ['id' => 1],
            $sideEffectResult
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'irhpApplication' => 1
            ],
            'messages' => [
                0 => 'Message from ResetIrhpPermitApplications',
                1 => 'IrhpApplication Licence Updated successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWhereCantMakeApp()
    {
        $applicationId = 1;
        $licenceId = 2;
        $licNo = 'OB1234567';

        $expectedExceptionMessage = sprintf(UpdateLicence::LICENCE_INVALID_MSG, $licenceId, $licNo);
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $data = [
            'id' => $applicationId,
            'licence' => $licenceId,
        ];
        $command = Command::create($data);

        $orgId = 1245;
        $organisation = m::mock(OrganisationEntity::class);
        $organisation->shouldReceive('getId')->once()->withNoArgs()->andReturn($orgId);

        $licenceOrganisation = m::mock(OrganisationEntity::class);
        $licenceOrganisation->shouldReceive('getId')->once()->withNoArgs()->andReturn($orgId);

        $identity = m::mock(IdentityInterface::class);
        $identity->shouldReceive('getUser->getOrganisationUsers->isEmpty')->once()->andReturnFalse();
        $identity->shouldReceive('getUser->getRelatedOrganisation')->once()->andReturn($organisation);

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('getIdentity')
            ->once()
            ->andReturn($identity);

        $irhpPermitStock = m::mock(IrhpPermitStock::class);

        $irhpApplication = m::mock(IrhpApplicationEntity::class);

        $irhpApplication->shouldReceive('getAssociatedStock')
            ->once()
            ->withNoArgs()
            ->andReturn($irhpPermitStock);
        $irhpApplication->shouldReceive('isMultiStock')
            ->once()
            ->withNoArgs()
            ->andReturnFalse();

        $this->repoMap['IrhpApplication']
            ->shouldReceive('fetchById')
            ->once()
            ->with($applicationId)
            ->andReturn($irhpApplication);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class);
        $licence
            ->shouldReceive('getRelatedOrganisation')
            ->once()
            ->withNoArgs()
            ->andReturn($licenceOrganisation);

        $licence->shouldReceive('canMakeIrhpApplication')
            ->once()
            ->with($irhpPermitStock, $irhpApplication)
            ->andReturnFalse();

        $licence->shouldReceive('getId')->once()->withNoArgs()->andReturn($licenceId);
        $licence->shouldReceive('getLicNo')->once()->withNoArgs()->andReturn($licNo);

        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->once()
            ->with($licenceId)
            ->andReturn($licence);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWhereLicenceDoesNotMatchOrg()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(UpdateLicence::LICENCE_ORG_MSG);

        $licenceId = 2;
        $data = ['licence' => $licenceId];
        $command = Command::create($data);

        $organisation = m::mock(OrganisationEntity::class);
        $organisation->shouldReceive('getId')->once()->andReturn(1245);

        $licenceOrganisation = m::mock(OrganisationEntity::class);
        $licenceOrganisation->shouldReceive('getId')->once()->andReturn(1246);

        $identity = m::mock(IdentityInterface::class);
        $identity->shouldReceive('getUser->getOrganisationUsers->isEmpty')->once()->andReturnFalse();
        $identity->shouldReceive('getUser->getRelatedOrganisation')->once()->andReturn($organisation);

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('getIdentity')
            ->once()
            ->withNoArgs()
            ->andReturn($identity);

        $licence = m::mock(LicenceEntity::class);
        $licence
            ->shouldReceive('getRelatedOrganisation')
            ->once()
            ->withNoArgs()
            ->andReturn($licenceOrganisation);

        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->once()
            ->with($licenceId)
            ->andReturn($licence);

        $this->sut->handleCommand($command);
    }
}
