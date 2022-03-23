<?php

/**
 * Update User Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Email\SendUserTemporaryPassword as SendUserTemporaryPasswordDto;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as EnqueueFileCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\User\UpdateUser as Sut;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails;
use Dvsa\Olcs\Api\Domain\Repository\EventHistory;
use Dvsa\Olcs\Api\Domain\Repository\EventHistoryType;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\Repository\User;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistory as EventHistoryEntity;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistoryType as EventHistoryTypeEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser as OrganisationUserEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Api\Entity\User\Permission as PermissionEntity;
use Dvsa\Olcs\Api\Entity\User\Role as RoleEntity;
use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Rbac\Identity;
use Dvsa\Olcs\Api\Rbac\JWTIdentityProvider;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Dvsa\Olcs\Api\Service\OpenAm\UserInterface;
use Dvsa\Olcs\Auth\Service\PasswordService;
use Dvsa\Olcs\Transfer\Command\User\UpdateUser as Cmd;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Mockery as m;
use Mockery\Mock;
use ZfcRbac\Service\AuthorizationService;

/**
 * Update User Test
 */
class UpdateUserOpenAMTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new Sut();
        $this->mockRepo('User', User::class);
        $this->mockRepo('Application', Application::class);
        $this->mockRepo('ContactDetails', ContactDetails::class);
        $this->mockRepo('Licence', Licence::class);
        $this->mockRepo('EventHistory', EventHistory::class);
        $this->mockRepo('EventHistoryType', EventHistoryType::class);

        $mockConfig = [
            'auth' => [
                'identity_provider' => PidIdentityProvider::class
            ]
        ];

        $this->mockedSmServices = [
            CacheEncryption::class => m::mock(CacheEncryption::class),
            AuthorizationService::class => m::mock(AuthorizationService::class),
            UserInterface::class => m::mock(UserInterface::class),
            ValidatableAdapterInterface::class => m::mock(ValidatableAdapterInterface::class),
            PasswordService::class => m::mock(PasswordService::class),
            'Config' => $mockConfig
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            ContactDetailsEntity::CONTACT_TYPE_USER
        ];

        $this->references = [
            Country::class => [
                'GB' => m::mock(Country::class)
            ],
            Team::class => [
                '1' => m::mock(Team::class)
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithNewContactDetails()
    {
        $userId = 111;

        $data = [
            'id' => 111,
            'version' => 1,
            'userType' => UserEntity::USER_TYPE_OPERATOR,
            'team' => 1,
            'licenceNumber' => '',
            'loginId' => 'login_id',
            'contactDetails' => [
                'emailAddress' => 'test1@test.me',
                'person' => [
                    'title' => m::mock(RefData::class),
                    'forename' => 'updated forename',
                    'familyName' => 'updated familyName',
                    'birthDate' => '1975-12-12',
                ],
                'address' => [
                    'addressLine1' => 'a12',
                    'addressLine2' => 'a23',
                    'addressLine3' => 'a34',
                    'addressLine4' => 'a45',
                    'town' => 'town',
                    'postcode' => 'LS1 2AB',
                    'countryCode' => m::mock(Country::class),
                ],
                'phoneContacts' => [
                    [
                        'phoneContactType' => m::mock(RefData::class),
                        'phoneNumber' => '111',
                    ],
                    [
                        'phoneContactType' => m::mock(RefData::class),
                        'phoneNumber' => '222',
                    ]
                ],
            ],
        ];

        $command = Cmd::create($data);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_MANAGE_USER_INTERNAL, null)
            ->andReturn(true)
            ->shouldReceive('getIdentity')
            ->andReturn($this->getMockIdentity());

        $this->mockedSmServices[UserInterface::class]->shouldReceive('updateUser')
            ->once()
            ->with('pid', 'login_id', 'test1@test.me', false);

        /** @var TeamEntity $user */
        $team = m::mock(Team::class)->makePartial();

        /** @var ContactDetailsEntity|m\Mock $contactDetails */
        $contactDetails = m::mock(ContactDetailsEntity::class)->makePartial();
        $contactDetails->shouldReceive('getEmailAddress')->andReturn('test1@test.me');


        /** @var UserEntity|m\Mock $user */
        $user = m::mock(UserEntity::class)->makePartial();
        $user->initCollections();
        $user->setId($userId);
        $user->setPid('pid');
        $user->setLoginId($data['loginId']);
        $user->setTeam($team);
        $user->shouldReceive('update')->once()->with($data)->andReturnSelf();
        $user->shouldReceive('getContactDetails')->twice()->andReturnNull();
        $user->shouldReceive('getContactDetails')->once()->andReturn($contactDetails);

        $this->repoMap['User']->shouldReceive('fetchById')
            ->once()
            ->with($userId, Query::HYDRATE_OBJECT, 1)
            ->andReturn($user)
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
                function (UserEntity $user) use (&$savedUser) {
                    $savedUser = $user;
                }
            );

        $this->expectedUserCacheClear([$userId]);
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'user' => $userId,
            ],
            'messages' => [
                'User updated successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithUpdatedContactDetails()
    {
        $userId = 111;
        $licenceNumber = 'LIC123';

        $data = [
            'id' => 111,
            'version' => 1,
            'userType' => UserEntity::USER_TYPE_OPERATOR,
            'team' => 1,
            'licenceNumber' => $licenceNumber,
            'loginId' => 'login_id',
            'contactDetails' => [
                'emailAddress' => 'test1@test.me',
                'person' => [
                    'title' => m::mock(RefData::class),
                    'forename' => 'updated forename',
                    'familyName' => 'updated familyName',
                    'birthDate' => '1975-12-12',
                ],
                'address' => [
                    'addressLine1' => 'a12',
                    'addressLine2' => 'a23',
                    'addressLine3' => 'a34',
                    'addressLine4' => 'a45',
                    'town' => 'town',
                    'postcode' => 'LS1 2AB',
                    'countryCode' => m::mock(Country::class),
                ],
                'phoneContacts' => [
                    [
                        'phoneContactType' => m::mock(RefData::class),
                        'phoneNumber' => '111',
                    ],
                    [
                        'id' => 999,
                        'phoneContactType' => m::mock(RefData::class),
                        'phoneNumber' => '222',
                    ]
                ],
            ],
            'accountDisabled' => 'Y',
        ];

        $command = Cmd::create($data);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_MANAGE_USER_INTERNAL, null)
            ->andReturn(true)
            ->shouldReceive('getIdentity')
            ->andReturn($this->getMockIdentity());

        $this->mockedSmServices[UserInterface::class]
            ->shouldReceive('updateUser')
            ->once()
            ->with('pid', 'login_id', 'test1@test.me', true)
            ->shouldReceive('resetPassword')
            ->never();

        /** @var ContactDetailsEntity|m\Mock $contactDetails */
        $contactDetails = m::mock(ContactDetailsEntity::class)->makePartial();
        $contactDetails->shouldReceive('update')
            ->once()
            ->with($data['contactDetails'])
            ->andReturnSelf();

        $contactDetails->shouldReceive('getEmailAddress')->andReturn('test1@test.me');

        /** @var TeamEntity $user */
        $team = m::mock(Team::class)->makePartial();

        /** @var UserEntity|m\Mock $user */
        $user = m::mock(UserEntity::class)->makePartial();
        $user->initCollections();
        $user->setId($userId);
        $user->setPid('pid');
        $user->setLoginId($data['loginId']);
        $user->setTeam($team);
        $user->setContactDetails($contactDetails);
        $user->shouldReceive('update')->once()->with($data)->andReturnSelf();
        $user->shouldReceive('isDisabled')->andReturnTrue();

        $this->repoMap['User']->shouldReceive('fetchById')
            ->once()
            ->with($userId, Query::HYDRATE_OBJECT, 1)
            ->andReturn($user)
            ->shouldReceive('populateRefDataReference')
            ->once()
            ->andReturn($data);

        $this->repoMap['ContactDetails']->shouldReceive('populateRefDataReference')
            ->once()
            ->with($data['contactDetails'])
            ->andReturn($data['contactDetails']);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->shouldReceive('getOrganisation')
            ->once();

        $this->repoMap['Licence']->shouldReceive('fetchByLicNo')
            ->once()
            ->with($licenceNumber)
            ->andReturn($licence);

        /** @var UserEntity $savedUser */
        $savedUser = null;

        $this->repoMap['User']->shouldReceive('save')
            ->once()
            ->with(m::type(UserEntity::class))
            ->andReturnUsing(
                function (UserEntity $user) use (&$savedUser) {
                    $savedUser = $user;
                }
            );

        $this->expectedUserCacheClear([$userId]);
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'user' => $userId,
            ],
            'messages' => [
                'User updated successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertSame(
            $contactDetails,
            $savedUser->getContactDetails()
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testHandleCommandWithPasswordResetByEmail()
    {
        $userId = 111;

        $data = [
            'id' => 111,
            'version' => 1,
            'userType' => UserEntity::USER_TYPE_OPERATOR,
            'loginId' => 'login_id',
            'contactDetails' => [
                'emailAddress' => 'test1@test.me',
            ],
            'resetPassword' => Sut::RESET_PASSWORD_BY_EMAIL,
        ];

        $command = Cmd::create($data);

        $loggedInUserId = 1000;

        /** @var UserEntity $user */
        $loggedInUser = m::mock(UserEntity::class)
            ->makePartial()
            ->shouldReceive('isAllowedToPerformActionOnRoles')
            ->andReturn(true)
            ->shouldReceive('setId')
            ->getMock();

        $loggedInUser->setId($loggedInUserId);

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_MANAGE_USER_INTERNAL, null)
            ->andReturn(true)
            ->shouldReceive('getIdentity->getUser')
            ->andReturn($loggedInUser);

        $this->mockedSmServices[UserInterface::class]
            ->shouldReceive('updateUser')
            ->once()
            ->with('pid', 'login_id', 'test1@test.me', false)
            ->shouldReceive('resetPassword')
            ->once()
            ->with('pid', m::type('callable'))
            ->andReturnUsing(
                function ($pid, $callback) {
                    $params = [
                        'password' => 'GENERATED_PASSWORD'
                    ];
                    $callback($params);
                }
            );

        /** @var ContactDetailsEntity|m\Mock $contactDetails */
        $contactDetails = m::mock(ContactDetailsEntity::class)->makePartial();
        $contactDetails->shouldReceive('update')
            ->once()
            ->with($data['contactDetails'])
            ->andReturnSelf();
        $contactDetails->shouldReceive('getEmailAddress')->andReturn('test1@test.me');

        /** @var UserEntity $user */
        $user = m::mock(UserEntity::class)->makePartial();
        $user->initCollections();
        $user->setId($userId);
        $user->setPid('pid');
        $user->setLoginId($data['loginId']);
        $user->setContactDetails($contactDetails);
        $user->shouldReceive('update')->once()->with($data)->andReturnSelf();
        $user->shouldReceive('isDisabled')->andReturnfalse();

        $this->repoMap['User']->shouldReceive('fetchById')
            ->once()
            ->with($userId, Query::HYDRATE_OBJECT, 1)
            ->andReturn($user)
            ->shouldReceive('populateRefDataReference')
            ->once()
            ->andReturn($data);

        $this->repoMap['ContactDetails']->shouldReceive('populateRefDataReference')
            ->once()
            ->with($data['contactDetails'])
            ->andReturn($data['contactDetails']);

        $this->repoMap['User']->shouldReceive('save')
            ->once()
            ->with(m::type(UserEntity::class));

        $this->expectedSideEffect(
            SendUserTemporaryPasswordDto::class,
            [
                'user' => $userId,
                'password' => 'GENERATED_PASSWORD',
            ],
            new Result()
        );

        $eventHistoryType = m::mock(EventHistoryTypeEntity::class)->makePartial();
        $this->repoMap['EventHistoryType']
            ->shouldReceive('fetchOneByEventCode')
            ->once()
            ->with(EventHistoryTypeEntity::EVENT_CODE_PASSWORD_RESET)
            ->andReturn($eventHistoryType);

        $this->repoMap['EventHistory']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(EventHistoryEntity::class))
            ->andReturnUsing(
                function (EventHistoryEntity $eventHistory) use ($loggedInUser, $eventHistoryType, $user) {
                    $this->assertSame($loggedInUser, $eventHistory->getUser());
                    $this->assertSame($eventHistoryType, $eventHistory->getEventHistoryType());
                    $this->assertSame('By email', $eventHistory->getEventData());
                    $this->assertInstanceOf(\DateTime::class, $eventHistory->getEventDatetime());
                    $this->assertSame($user, $eventHistory->getAccount());
                }
            );

        $this->mockedSmServices[PasswordService::class]
            ->shouldReceive('generatePassword')
            ->andReturn('password');

        $this->expectedUserCacheClear([$userId]);
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'user' => $userId,
            ],
            'messages' => [
                'User updated successfully',
                'Temporary password successfully generated and saved'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testHandleCommandWithPasswordResetByPost()
    {
        $userId = 111;

        $data = [
            'id' => 111,
            'version' => 1,
            'userType' => UserEntity::USER_TYPE_OPERATOR,
            'loginId' => 'login_id',
            'contactDetails' => [
                'emailAddress' => 'test1@test.me',
            ],
            'resetPassword' => Sut::RESET_PASSWORD_BY_POST,
        ];

        $command = Cmd::create($data);

        $loggedInUserId = 1000;

        /** @var UserEntity $user */
        $loggedInUser = m::mock(UserEntity::class)
            ->makePartial()
            ->shouldReceive('isAllowedToPerformActionOnRoles')
            ->andReturn(true)
            ->shouldReceive('setId')
            ->getMock();

        $loggedInUser->setId($loggedInUserId);

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_MANAGE_USER_INTERNAL, null)
            ->andReturn(true)
            ->shouldReceive('getIdentity->getUser')
            ->andReturn($loggedInUser);

        $this->mockedSmServices[UserInterface::class]
            ->shouldReceive('updateUser')
            ->once()
            ->with('pid', 'login_id', 'test1@test.me', false)
            ->shouldReceive('resetPassword')
            ->once()
            ->with('pid', m::type('callable'))
            ->andReturnUsing(
                function ($pid, $callback) {
                    $params = [
                        'password' => 'GENERATED_PASSWORD'
                    ];
                    $callback($params);
                }
            );

        /** @var ContactDetailsEntity|m\Mock $contactDetails */
        $contactDetails = m::mock(ContactDetailsEntity::class)->makePartial();
        $contactDetails->shouldReceive('ssetId');
        $contactDetails->shouldReceive('getEmailAddress')->andReturn('test1@test.me');


        $licId = 123;
        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId($licId);
        $licence->setStatus(new RefData(LicenceEntity::LICENCE_STATUS_NOT_SUBMITTED));

        /** @var OrganisationEntity $org */
        $org = m::mock(OrganisationEntity::class)->makePartial();
        $org->setLicences(new ArrayCollection([$licence]));

        /** @var OrganisationEntity $org */
        $orgUser = m::mock(OrganisationUserEntity::class)->makePartial();
        $orgUser->setOrganisation($org);

        /** @var UserEntity|m\Mock $user */
        $user = m::mock(UserEntity::class)->makePartial();
        $user->initCollections();
        $user->setId($userId);
        $user->setPid('pid');
        $user->setLoginId($data['loginId']);
        $user->setContactDetails($contactDetails);
        $user->setOrganisationUsers(new ArrayCollection([$orgUser]));
        $user->shouldReceive('update')->once()->with($data)->andReturnSelf();
        $user->shouldReceive('getContactDetails')->twice()->andReturnNull();
        $user->shouldReceive('getContactDetails')->once()->andReturn($contactDetails);

        $this->repoMap['User']->shouldReceive('fetchById')
            ->once()
            ->with($userId, Query::HYDRATE_OBJECT, 1)
            ->andReturn($user)
            ->shouldReceive('populateRefDataReference')
            ->once()
            ->andReturn($data);

        $this->repoMap['ContactDetails']->shouldReceive('populateRefDataReference')
            ->once()
            ->with($data['contactDetails'])
            ->andReturn($data['contactDetails']);

        $this->repoMap['User']->shouldReceive('save')
            ->once()
            ->with(m::type(UserEntity::class));

        $documentId = 333;
        $generateAndStoreResult = new Result();
        $generateAndStoreResult->addId('document', $documentId);

        $this->expectedSideEffect(
            GenerateAndStore::class,
            [
                'template' => 'SELF_SERVICE_NEW_PASSWORD',
                'query' => [
                    'licence' => $licId
                ],
                'knownValues' => [
                    'SELF_SERVICE_PASSWORD' => 'GENERATED_PASSWORD'
                ],
                'description' => 'Reset password letter',
                'category' => CategoryEntity::CATEGORY_APPLICATION,
                'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_APPLICATION_OTHER_DOCUMENTS,
                'isExternal' => false,
                'isScan' => false
            ],
            $generateAndStoreResult
        );

        $this->expectedSideEffect(
            EnqueueFileCommand::class,
            [
                'documentId' => $documentId,
                'jobName' => 'New temporary password'
            ],
            new Result()
        );

        $eventHistoryType = m::mock(EventHistoryTypeEntity::class)->makePartial();
        $this->repoMap['EventHistoryType']
            ->shouldReceive('fetchOneByEventCode')
            ->once()
            ->with(EventHistoryTypeEntity::EVENT_CODE_PASSWORD_RESET)
            ->andReturn($eventHistoryType);

        $this->repoMap['EventHistory']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(EventHistoryEntity::class))
            ->andReturnUsing(
                function (EventHistoryEntity $eventHistory) use ($loggedInUser, $eventHistoryType, $user) {
                    $this->assertSame($loggedInUser, $eventHistory->getUser());
                    $this->assertSame($eventHistoryType, $eventHistory->getEventHistoryType());
                    $this->assertSame('By post', $eventHistory->getEventData());
                    $this->assertInstanceOf(\DateTime::class, $eventHistory->getEventDatetime());
                    $this->assertSame($user, $eventHistory->getAccount());
                }
            );

        $this->expectedUserCacheClear([$userId]);

        $this->mockedSmServices[PasswordService::class]
            ->shouldReceive('generatePassword')
            ->andReturn('password');

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'user' => $userId,
            ],
            'messages' => [
                'User updated successfully',
                'Temporary password successfully generated and saved'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandForTm()
    {
        $userId = 111;
        $applicationId = 3;

        $data = [
            'id' => 111,
            'version' => 1,
            'userType' => UserEntity::USER_TYPE_TRANSPORT_MANAGER,
            'application' => $applicationId,
            'transport_manager' => 1,
            'loginId' => 'login_id',
            'contactDetails' => [
                'emailAddress' => 'test1@test.me',
                'person' => [
                    'title' => m::mock(RefData::class),
                    'forename' => 'updated forename',
                    'familyName' => 'updated familyName',
                    'birthDate' => '1975-12-12',
                ],
                'address' => [
                    'addressLine1' => 'a12',
                    'addressLine2' => 'a23',
                    'addressLine3' => 'a34',
                    'addressLine4' => 'a45',
                    'town' => 'town',
                    'postcode' => 'LS1 2AB',
                    'countryCode' => m::mock(Country::class),
                ],
                'phoneContacts' => [
                    [
                        'phoneContactType' => m::mock(RefData::class),
                        'phoneNumber' => '111',
                    ],
                    [
                        'id' => 999,
                        'phoneContactType' => m::mock(RefData::class),
                        'phoneNumber' => '222',
                    ]
                ],
            ],
        ];

        $command = Cmd::create($data);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_MANAGE_USER_INTERNAL, null)
            ->andReturn(true)
            ->shouldReceive('getIdentity')
            ->andReturn($this->getMockIdentity());

        $this->mockedSmServices[UserInterface::class]
            ->shouldReceive('updateUser')
            ->once()
            ->with('pid', 'login_id', 'test1@test.me', false)
            ->shouldReceive('resetPassword')
            ->never();

        /** @var ContactDetailsEntity|Mock $contactDetails */
        $contactDetails = m::mock(ContactDetailsEntity::class)->makePartial();
        $contactDetails->shouldReceive('update')
            ->once()
            ->with($data['contactDetails'])
            ->andReturnSelf();
        $contactDetails->shouldReceive('getEmailAddress')->andReturn('test1@test.me');

        /** @var TeamEntity $user */
        $team = m::mock(Team::class)->makePartial();

        /** @var UserEntity $user */
        $user = m::mock(UserEntity::class)->makePartial();
        $user->initCollections();
        $user->setId($userId);
        $user->setPid('pid');
        $user->setLoginId($data['loginId']);
        $user->setTeam($team);
        $user->setContactDetails($contactDetails);
        $user->shouldReceive('update')->once()->with($data)->andReturnSelf();

        $this->repoMap['User']->shouldReceive('fetchById')
            ->once()
            ->with($userId, Query::HYDRATE_OBJECT, 1)
            ->andReturn($user)
            ->shouldReceive('populateRefDataReference')
            ->once()
            ->andReturn($data);

        $this->repoMap['ContactDetails']->shouldReceive('populateRefDataReference')
            ->once()
            ->with($data['contactDetails'])
            ->andReturn($data['contactDetails']);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->shouldReceive('getOrganisation')
            ->once();

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->shouldReceive('getLicence')
            ->once()
            ->andReturn($licence);

        $this->repoMap['Application']->shouldReceive('fetchWithLicenceAndOrg')
            ->once()
            ->with($applicationId)
            ->andReturn($application);

        /** @var UserEntity $savedUser */
        $savedUser = null;

        $this->repoMap['User']->shouldReceive('save')
            ->once()
            ->with(m::type(UserEntity::class))
            ->andReturnUsing(
                function (UserEntity $user) use (&$savedUser) {
                    $savedUser = $user;
                }
            );

        $this->expectedUserCacheClear([$userId]);
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'user' => $userId,
            ],
            'messages' => [
                'User updated successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertSame(
            $contactDetails,
            $savedUser->getContactDetails()
        );
    }

    public function testHandleCommandThrowsIncorrectPermissionException()
    {
        $data = [
            'id' => 111,
            'version' => 1,
        ];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_MANAGE_USER_INTERNAL, null)
            ->andReturn(false)
            ->shouldReceive('getIdentity')
            ->andReturn($this->getMockIdentity());

        $this->repoMap['User']
            ->shouldReceive('fetchById')
            ->never()
            ->shouldReceive('save')
            ->never();

        $command = Cmd::create($data);

        $this->expectException(ForbiddenException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandThrowsRolesPermissionException()
    {
        $userId = 111;

        $data = [
            'id' => 111,
            'version' => 1,
            'loginId' => 'loginId',
            'roles' => [RoleEntity::ROLE_SYSTEM_ADMIN],
        ];

        $command = Cmd::create($data);

        $loggedInUserId = 1000;

        /** @var RoleEntity $loggedInUserRole */
        $loggedInUserRole = m::mock(RoleEntity::class)->makePartial();
        $loggedInUserRole->setRole(RoleEntity::ROLE_INTERNAL_ADMIN);

        /** @var UserEntity $loggedInUser */
        $loggedInUser = m::mock(UserEntity::class)->makePartial();
        $loggedInUser->initCollections();
        $loggedInUser->setId($loggedInUserId);
        $loggedInUser->addRoles($loggedInUserRole);

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_MANAGE_USER_INTERNAL, null)
            ->andReturn(true)
            ->shouldReceive('getIdentity->getUser')
            ->once()
            ->andReturn($loggedInUser);

        /** @var RoleEntity $userRole */
        $userRole = m::mock(RoleEntity::class)->makePartial();
        $userRole->setRole(RoleEntity::ROLE_INTERNAL_ADMIN);

        /** @var UserEntity $user */
        $user = m::mock(UserEntity::class)->makePartial();
        $user->initCollections();
        $user->setId($userId);
        $user->setLoginId('loginId');
        $user->addRoles($userRole);

        $this->repoMap['User']->shouldReceive('fetchById')
            ->once()
            ->with($userId, Query::HYDRATE_OBJECT, 1)
            ->andReturn($user)
            ->shouldReceive('save')
            ->never();

        $this->expectException(ValidationException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandThrowsRolesPermissionErrorWhenAttemptingToUpdateUserWithHigherRole()
    {
        $userId = 111;

        $data = [
            'id' => 111,
            'version' => 1,
            'loginId' => 'loginId',
            'roles' => [RoleEntity::ROLE_INTERNAL_ADMIN],
            'contactDetails' => [
                'emailAddress' => 'changed-email@example.com'
            ]
        ];

        $command = Cmd::create($data);

        $loggedInUserId = 1000;

        /** @var RoleEntity $loggedInUserRole */
        $loggedInUserRole = m::mock(RoleEntity::class)->makePartial();
        $loggedInUserRole->setRole(RoleEntity::ROLE_INTERNAL_CASE_WORKER);

        /** @var UserEntity $loggedInUser */
        $loggedInUser = m::mock(UserEntity::class)->makePartial();
        $loggedInUser->initCollections();
        $loggedInUser->setId($loggedInUserId);
        $loggedInUser->addRoles($loggedInUserRole);

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_MANAGE_USER_INTERNAL, null)
            ->andReturn(true)
            ->shouldReceive('getIdentity->getUser')
            ->once()
            ->andReturn($loggedInUser);

        /** @var RoleEntity $userRole */
        $userRole = m::mock(RoleEntity::class)->makePartial();
        $userRole->setRole(RoleEntity::ROLE_INTERNAL_ADMIN);

        /** @var UserEntity $user */
        $user = m::mock(UserEntity::class)->makePartial();
        $user->initCollections();
        $user->setId($userId);
        $user->setLoginId('loginId');
        $user->addRoles($userRole);

        $this->repoMap['User']->shouldReceive('fetchById')
            ->once()
            ->with($userId, Query::HYDRATE_OBJECT, 1)
            ->andReturn($user)
            ->shouldReceive('save')
            ->never();

        $this->expectException(ValidationException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandThrowsRolesPermissionLastUserException()
    {
        $userId = 111;

        $data = [
            'id' => 111,
            'version' => 1,
            'loginId' => 'loginId',
            'roles' => [RoleEntity::ROLE_INTERNAL_ADMIN],
        ];

        $command = Cmd::create($data);

        $loggedInUserId = 1000;

        /** @var RoleEntity $loggedInUserRole */
        $loggedInUserRole = m::mock(RoleEntity::class)->makePartial();
        $loggedInUserRole->setRole(RoleEntity::ROLE_SYSTEM_ADMIN);

        /** @var UserEntity $loggedInUser */
        $loggedInUser = m::mock(UserEntity::class)->makePartial();
        $loggedInUser->initCollections();
        $loggedInUser->setId($loggedInUserId);
        $loggedInUser->addRoles($loggedInUserRole);

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_MANAGE_USER_INTERNAL, null)
            ->andReturn(true)
            ->shouldReceive('getIdentity->getUser')
            ->once()
            ->andReturn($loggedInUser);

        /** @var RoleEntity $userRole */
        $userRole = m::mock(RoleEntity::class)->makePartial();
        $userRole->setRole(RoleEntity::ROLE_SYSTEM_ADMIN);

        /** @var UserEntity $user */
        $user = m::mock(UserEntity::class)->makePartial();
        $user->initCollections();
        $user->setId($userId);
        $user->setLoginId('loginId');
        $user->addRoles($userRole);

        $this->repoMap['User']->shouldReceive('fetchById')
            ->once()
            ->with($userId, Query::HYDRATE_OBJECT, 1)
            ->andReturn($user)
            ->shouldReceive('fetchUsersCountByRole')
            ->once()
            ->with(RoleEntity::ROLE_SYSTEM_ADMIN)
            ->andReturn(1)
            ->shouldReceive('save')
            ->never();

        $this->expectException(ValidationException::class);

        $this->sut->handleCommand($command);
    }

    private function getMockIdentity()
    {
        $mockUser = m::mock(UserEntity::class)
            ->shouldReceive('isAllowedToPerformActionOnRoles')
            ->andReturn(true)
            ->getMock();

        return m::mock(Identity::class)
            ->shouldReceive('getUser')
            ->andReturn($mockUser)
            ->getMock();
    }
}
