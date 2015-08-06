<?php

/**
 * CreateForResponsibilities Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication\CreateForResponsibilities as CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication as TransportManagerApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Transfer\Command\TransportManagerApplication\CreateForResponsibilities as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication as TransportManagerApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * CreateForResponsibilities Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CreateForResponsibilitiesTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('TransportManagerApplication', TransportManagerApplicationRepo::class);
        $this->mockRepo('Application', ApplicationRepo::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [TransportManagerApplicationEntity::STATUS_POSTAL_APPLICATION];

        $this->references = [
            ApplicationEntity::class => [
                123 => m::mock(ApplicationEntity::class)
            ],
            TransportManagerEntity::class => [
                456 => m::mock(TransportManagerEntity::class)
            ]
        ];

        parent::initReferences();
    }

    public function testCommandHandler()
    {
        $this->mockAuthService();

        $command = Cmd::create(
            [
                'application' => 1,
                'transportManager' => 2
            ]
        );

        $mockApplication = m::mock()
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                ->shouldReceive('getLicenceType')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('getId')
                    ->andReturn(LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL)
                    ->once()
                    ->getMock()
                )
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $this->repoMap['Application']
            ->shouldReceive('fetchWithLicence')
            ->with(1)
            ->andReturn($mockApplication)
            ->once()
            ->shouldReceive('fetchWithTmLicences')
            ->with(1)
            ->andReturn([])
            ->getMock();

        $this->repoMap['TransportManagerApplication']
            ->shouldReceive('fetchByTmAndApplication')
            ->with(2, 1)
            ->andReturn(null)
            ->once()
            ->getMock();

        $transportManagerApplication = null;

        $this->repoMap['TransportManagerApplication']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(TransportManagerApplicationEntity::class))
            ->andReturnUsing(
                function (TransportManagerApplicationEntity $tma) use (&$transportManagerApplication) {
                    $tma->setId(111);
                    $transportManagerApplication = $tma;
                }
            );

        $res = $this->sut->handleCommand($command);
        $this->assertEquals(
            [
                'id'=> [
                    'transportManagerApplication' => 111
                ],
                'messages' => [
                    'Transport Manager Application created successfully'
                ]
            ],
            $res->toArray()
        );
    }

    public function testCommandHandlerInvalidLicence()
    {
        $this->mockAuthService();

        $expectedErrors = [
            'application' =>  'A transport manager cannot be added to a restricted licence'
        ];
        $this->setExpectedException(ValidationException::class, $expectedErrors);

        $command = Cmd::create(
            [
                'application' => 1,
                'transportManager' => 2
            ]
        );

        $mockApplication = m::mock()
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                ->shouldReceive('getLicenceType')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('getId')
                    ->andReturn(LicenceEntity::LICENCE_TYPE_RESTRICTED)
                    ->once()
                    ->getMock()
                )
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $this->repoMap['Application']
            ->shouldReceive('fetchWithLicence')
            ->with(1)
            ->andReturn($mockApplication)
            ->once()
            ->getMock();

        $this->sut->handleCommand($command);
    }

    public function testCommandHandlerNoApplication()
    {
        $this->mockAuthService();

        $expectedErrors = [
            'application' =>  'The application ID is not valid'
        ];
        $this->setExpectedException(ValidationException::class, $expectedErrors);

        $command = Cmd::create(
            [
                'application' => 1,
                'transportManager' => 2
            ]
        );

        $this->repoMap['Application']
            ->shouldReceive('fetchWithLicence')
            ->with(1)
            ->andThrow(\Exception::class)
            ->once()
            ->getMock();

        $this->sut->handleCommand($command);
    }

    public function testCommandHandlerTmaExist()
    {
        $this->mockAuthService();

        $expectedErrors = [
                    'application' =>  'The transport manager is already linked to this application'
        ];
        $this->setExpectedException(ValidationException::class, $expectedErrors);

        $command = Cmd::create(
            [
                'application' => 1,
                'transportManager' => 2
            ]
        );

        $mockApplication = m::mock()
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                ->shouldReceive('getLicenceType')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('getId')
                    ->andReturn(LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL)
                    ->once()
                    ->getMock()
                )
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $this->repoMap['Application']
            ->shouldReceive('fetchWithLicence')
            ->with(1)
            ->andReturn($mockApplication)
            ->once()
            ->getMock();

        $this->repoMap['TransportManagerApplication']
            ->shouldReceive('fetchByTmAndApplication')
            ->with(2, 1)
            ->andReturn(['tma'])
            ->once()
            ->getMock();

        $this->sut->handleCommand($command);
    }

    protected function mockAuthService()
    {
        /** @var Team $mockTeam */
        $mockTeam = m::mock(Team::class)->makePartial();
        $mockTeam->setId(2);

        /** @var User $mockUser */
        $mockUser = m::mock(User::class)->makePartial();
        $mockUser->setId(1);
        $mockUser->setTeam($mockTeam);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);
    }
}
