<?php

/**
 * Create User Selfserve Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\User;

use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Dvsa\Olcs\Api\Service\OpenAm\UserInterface;
use Dvsa\Olcs\Auth\Service\PasswordService;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Email\SendUserCreated as SendUserCreatedDto;
use Dvsa\Olcs\Api\Domain\Command\Email\SendUserTemporaryPassword as SendUserTemporaryPasswordDto;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\User\CreateUserSelfserve as Sut;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails;
use Dvsa\Olcs\Api\Domain\Repository\User;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LocalAuthorityEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser as OrganisationUserEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;
use Dvsa\Olcs\Transfer\Command\User\CreateUserSelfserve as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Create User Selfserve Test
 */
class CreateUserSelfserveTest extends CommandHandlerTestCase
{
    /**
     * @var ValidatableAdapterInterface|m\LegacyMockInterface|m\MockInterface
     */
    private $mockedAdapter;

    public function setUp(): void
    {
        $mockedPasswordService = m::mock(PasswordService::class);
        $mockedPasswordService->shouldReceive('generatePassword')->andReturn('abcdef123456');

        $this->mockedAdapter = m::mock(ValidatableAdapterInterface::class);

        $this->sut = new Sut($mockedPasswordService, $this->mockedAdapter);
        $this->mockRepo('User', User::class);
        $this->mockRepo('ContactDetails', ContactDetails::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class),
            UserInterface::class => m::mock(UserInterface::class)
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            ContactDetailsEntity::CONTACT_TYPE_USER
        ];

        parent::initReferences();
    }

    public function commonHandleCommandTest()
    {
        $userId = 111;

        $data = [
            'loginId' => 'login_id',
            'contactDetails' => [
                'emailAddress' => 'test1@test.me',
                'person' => [
                    'forename' => 'updated forename',
                    'familyName' => 'updated familyName',
                ],
            ],
            'permission' => UserEntity::PERMISSION_ADMIN,
        ];


        $this->mockedAdapter->shouldReceive('register')->once()->byDefault();

        $command = Cmd::create($data);

        $this->repoMap['User']
            ->shouldReceive('disableSoftDeleteable')
            ->once()
            ->shouldReceive('fetchByLoginId')
            ->once()
            ->with($data['loginId'])
            ->andReturn([])
            ->shouldReceive('enableSoftDeleteable')
            ->once();

        $this->repoMap['User']
            ->shouldReceive('populateRefDataReference')
            ->once()
            ->andReturn($data);

        $this->repoMap['ContactDetails']->shouldReceive('populateRefDataReference')
            ->once()
            ->with($data['contactDetails'])
            ->andReturn($data['contactDetails']);

        /** @var UserEntity $savedUser */
        $savedUser = null;

        $this->repoMap['User']->shouldReceive('save')
            ->once()
            ->with(m::type(UserEntity::class))
            ->andReturnUsing(
                function (UserEntity $user) use (&$savedUser, $userId) {
                    $user->setId($userId);
                    $savedUser = $user;

                    $this->expectedSideEffect(
                        SendUserCreatedDto::class,
                        [
                            'user' => $userId
                        ],
                        new Result()
                    );

                    $this->expectedSideEffect(
                        SendUserTemporaryPasswordDto::class,
                        [
                            'user' => $userId,
                            'password' => 'abcdef123456',
                        ],
                        new Result()
                    );
                }
            )->byDefault();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'user' => $userId,
            ],
            'messages' => [
                'User created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertInstanceOf(ContactDetailsEntity::class, $savedUser->getContactDetails());
        $this->assertEquals(
            ContactDetailsEntity::CONTACT_TYPE_USER,
            $savedUser->getContactDetails()->getContactType()->getId()
        );
        $this->assertEquals(
            $data['contactDetails']['emailAddress'],
            $savedUser->getContactDetails()->getEmailAddress()
        );

        return $savedUser;
    }

    public function testHandleCommandForPartner()
    {
        /** @var ContactDetailsEntity $partnerContactDetails */
        $partnerContactDetails = m::mock(ContactDetailsEntity::class)->makePartial();
        $partnerContactDetails->setId(1000);

        /** @var UserEntity $currentUser */
        $currentUser = m::mock(UserEntity::class)->makePartial();
        $currentUser->setId(222);
        $currentUser->setPartnerContactDetails($partnerContactDetails);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        $savedUser = $this->commonHandleCommandTest();

        $this->assertEquals(UserEntity::USER_TYPE_PARTNER, $savedUser->getUserType());
    }

    public function testHandleCommandForLocalAuthority()
    {
        /** @var LocalAuthorityEntity $localAuthority */
        $localAuthority = m::mock(LocalAuthorityEntity::class)->makePartial();
        $localAuthority->setId(1000);

        /** @var UserEntity $currentUser */
        $currentUser = m::mock(UserEntity::class)->makePartial();
        $currentUser->setId(222);
        $currentUser->setLocalAuthority($localAuthority);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        $savedUser = $this->commonHandleCommandTest();

        $this->assertEquals(UserEntity::USER_TYPE_LOCAL_AUTHORITY, $savedUser->getUserType());
    }

    public function testHandleCommandForOperator()
    {
        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(1000);

        /** @var OrganisationUserEntity $organisation */
        $organisationUser = m::mock(OrganisationUserEntity::class)->makePartial();
        $organisationUser->setOrganisation($organisation);

        /** @var UserEntity $currentUser */
        $currentUser = new UserEntity('pid', UserEntity::USER_TYPE_OPERATOR);
        $currentUser->setId(222);
        $currentUser->getOrganisationUsers()->add($organisationUser);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        $savedUser = $this->commonHandleCommandTest();

        $this->assertEquals(UserEntity::USER_TYPE_OPERATOR, $savedUser->getUserType());
    }

    public function testHandleCommandForTm()
    {
        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(1000);

        /** @var OrganisationUserEntity $organisation */
        $organisationUser = m::mock(OrganisationUserEntity::class)->makePartial();
        $organisationUser->setOrganisation($organisation);

        /** @var TransportManagerEntity $transportManager */
        $transportManager = m::mock(TransportManagerEntity::class)->makePartial();
        $transportManager->setId(777);

        /** @var UserEntity $currentUser */
        $currentUser = new UserEntity('pid', UserEntity::USER_TYPE_TRANSPORT_MANAGER);
        $currentUser->setId(222);
        $currentUser->setTransportManager($transportManager);
        $currentUser->getOrganisationUsers()->add($organisationUser);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        $savedUser = $this->commonHandleCommandTest();

        $this->assertEquals(UserEntity::USER_TYPE_OPERATOR, $savedUser->getUserType());
    }

    public function testHandleCommandThrowsIncorrectUserTypeException()
    {
        $this->expectException(\Dvsa\Olcs\Api\Domain\Exception\BadRequestException::class);

        $userId = 111;

        $data = [
            'loginId' => 'login_id',
            'contactDetails' => [
                'emailAddress' => 'test1@test.me',
                'person' => [
                    'forename' => 'updated forename',
                    'familyName' => 'updated familyName',
                ],
            ],
        ];

        $command = Cmd::create($data);

        $this->repoMap['User']
            ->shouldReceive('disableSoftDeleteable')
            ->once()
            ->shouldReceive('fetchByLoginId')
            ->once()
            ->with($data['loginId'])
            ->andReturn([])
            ->shouldReceive('enableSoftDeleteable')
            ->once();

        /** @var TeamEntity $user */
        $team = m::mock(Team::class)->makePartial();

        /** @var UserEntity $currentUser */
        $currentUser = m::mock(UserEntity::class)->makePartial();
        $currentUser->setId($userId);
        $currentUser->setTeam($team);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandThrowsUsernameExistsException()
    {
        $this->expectException(\Dvsa\Olcs\Api\Domain\Exception\ValidationException::class);

        $data = [
            'loginId' => 'login_id',
        ];

        $command = Cmd::create($data);

        $this->repoMap['User']
            ->shouldReceive('disableSoftDeleteable')
            ->once()
            ->shouldReceive('fetchByLoginId')
            ->once()
            ->with($data['loginId'])
            ->andReturn([m::mock(UserEntity::class)])
            ->shouldReceive('enableSoftDeleteable')
            ->once();

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandThrowsExceptionWhenUnableToStoreUser()
    {
        $this->expectException(\Exception::class);

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(1000);

        /** @var OrganisationUserEntity $organisation */
        $organisationUser = m::mock(OrganisationUserEntity::class)->makePartial();
        $organisationUser->setOrganisation($organisation);

        /** @var UserEntity $currentUser */
        $currentUser = new UserEntity('pid', UserEntity::USER_TYPE_OPERATOR);
        $currentUser->setId(222);
        $currentUser->getOrganisationUsers()->add($organisationUser);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        $userId = 111;

        $data = [
            'loginId' => 'login_id',
            'contactDetails' => [
                'emailAddress' => 'test1@test.me',
                'person' => [
                    'forename' => 'updated forename',
                    'familyName' => 'updated familyName',
                ],
            ],
            'permission' => UserEntity::PERMISSION_ADMIN,
        ];


        $this->mockedAdapter->shouldReceive('register')
            ->once()
            ->andThrow(ClientException::class);

        $command = Cmd::create($data);

        $this->repoMap['User']
            ->shouldReceive('disableSoftDeleteable')
            ->once()
            ->shouldReceive('fetchByLoginId')
            ->once()
            ->with($data['loginId'])
            ->andReturn([])
            ->shouldReceive('enableSoftDeleteable')
            ->once();

        $this->repoMap['User']
            ->shouldReceive('populateRefDataReference')
            ->once()
            ->andReturn($data);

        $this->repoMap['User']
            ->shouldReceive('delete')
            ->once();


        $this->repoMap['ContactDetails']->shouldReceive('populateRefDataReference')
            ->once()
            ->with($data['contactDetails'])
            ->andReturn($data['contactDetails']);

        /** @var UserEntity $savedUser */
        $savedUser = null;

        $this->repoMap['User']->shouldReceive('save')
            ->once()
            ->with(m::type(UserEntity::class));

        $this->sut->handleCommand($command);
    }
}
