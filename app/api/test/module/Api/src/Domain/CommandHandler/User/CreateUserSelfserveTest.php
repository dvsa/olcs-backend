<?php

/**
 * Create User Selfserve Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\User;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\User\CreateUserSelfserve as Sut;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails;
use Dvsa\Olcs\Api\Domain\Repository\User;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LocalAuthorityEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser as OrganisationUserEntity;
use Dvsa\Olcs\Api\Entity\User\Permission as PermissionEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Transfer\Command\User\CreateUserSelfserve as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;

/**
 * Create User Selfserve Test
 */
class CreateUserSelfserveTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Sut();
        $this->mockRepo('User', User::class);
        $this->mockRepo('ContactDetails', ContactDetails::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
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

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_MANAGE_USER_SELFSERVE, null)
            ->andReturn(true);

        $command = Cmd::create($data);

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
                }
            );

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
        $currentUser = new UserEntity(UserEntity::USER_TYPE_OPERATOR);
        $currentUser->setId(222);
        $currentUser->getOrganisationUsers()->add($organisationUser);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        $savedUser = $this->commonHandleCommandTest();

        $this->assertEquals(UserEntity::USER_TYPE_OPERATOR, $savedUser->getUserType());
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testHandleCommandThrowsIncorrectUserTypeException()
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
        ];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_MANAGE_USER_SELFSERVE, null)
            ->andReturn(true);

        $command = Cmd::create($data);

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

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testHandleCommandThrowsIncorrectPermissionException()
    {
        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_MANAGE_USER_SELFSERVE, null)
            ->andReturn(false);

        $command = Cmd::create(['data']);

        $this->sut->handleCommand($command);
    }
}
