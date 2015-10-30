<?php

/**
 * Update User Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Service\OpenAm\UserInterface;
use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\User\UpdateUser as Sut;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\Repository\User;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\User\Permission as PermissionEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\User\UpdateUser as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;

/**
 * Update User Test
 */
class UpdateUserTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Sut();
        $this->mockRepo('User', User::class);
        $this->mockRepo('Application', Application::class);
        $this->mockRepo('ContactDetails', ContactDetails::class);
        $this->mockRepo('Licence', Licence::class);

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
            ->andReturn(true);

        $this->mockedSmServices[UserInterface::class]->shouldReceive('updateUser')
            ->once()
            ->with('login_id', 'test1@test.me', false);

        /** @var TeamEntity $user */
        $team = m::mock(Team::class)->makePartial();

        /** @var UserEntity $user */
        $user = m::mock(UserEntity::class)->makePartial();
        $user->setId($userId);
        $user->setLoginId($data['loginId']);
        $user->setTeam($team);
        $user->setLoginId('login_id');
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

        $this->assertInstanceOf(ContactDetailsEntity::class, $savedUser->getContactDetails());
        $this->assertEquals(
            ContactDetailsEntity::CONTACT_TYPE_USER,
            $savedUser->getContactDetails()->getContactType()->getId()
        );
        $this->assertEquals(
            $data['contactDetails']['emailAddress'],
            $savedUser->getContactDetails()->getEmailAddress()
        );
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
            ->andReturn(true);

        $this->mockedSmServices[UserInterface::class]->shouldReceive('updateUser')
            ->once()
            ->with('login_id', 'test1@test.me', true);

        /** @var ContactDetailsEntity $contactDetails */
        $contactDetails = m::mock(ContactDetailsEntity::class)->makePartial();
        $contactDetails->shouldReceive('update')
            ->once()
            ->with($data['contactDetails'])
            ->andReturnSelf();

        /** @var TeamEntity $user */
        $team = m::mock(Team::class)->makePartial();

        /** @var UserEntity $user */
        $user = m::mock(UserEntity::class)->makePartial();
        $user->setId($userId);
        $user->setLoginId($data['loginId']);
        $user->setTeam($team);
        $user->setLoginId('login_id');
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
            ->andReturn(true);

        $this->mockedSmServices[UserInterface::class]->shouldReceive('updateUser')
            ->once()
            ->with('login_id', 'test1@test.me', false);

        /** @var ContactDetailsEntity $contactDetails */
        $contactDetails = m::mock(ContactDetailsEntity::class)->makePartial();
        $contactDetails->shouldReceive('update')
            ->once()
            ->with($data['contactDetails'])
            ->andReturnSelf();

        /** @var TeamEntity $user */
        $team = m::mock(Team::class)->makePartial();

        /** @var UserEntity $user */
        $user = m::mock(UserEntity::class)->makePartial();
        $user->setId($userId);
        $user->setLoginId($data['loginId']);
        $user->setTeam($team);
        $user->setLoginId('login_id');
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
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testHandleCommandThrowsIncorrectPermissionException()
    {
        $data = [
            'id' => 111,
            'version' => 1,
        ];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_MANAGE_USER_INTERNAL, null)
            ->andReturn(false);

        $this->repoMap['User']
            ->shouldReceive('fetchById')
            ->never()
            ->shouldReceive('save')
            ->never();

        $command = Cmd::create($data);

        $this->sut->handleCommand($command);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    public function testHandleCommandThrowsUsernameExistsException()
    {
        $userId = 111;

        $data = [
            'id' => 111,
            'version' => 1,
            'loginId' => 'updated login_id',
        ];

        $command = Cmd::create($data);

        /** @var UserEntity $user */
        $user = m::mock(UserEntity::class)->makePartial();
        $user->setId($userId);
        $user->setLoginId('loginId');

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_MANAGE_USER_INTERNAL, null)
            ->andReturn(true);

        $this->repoMap['User']->shouldReceive('fetchById')
            ->once()
            ->with($userId, Query::HYDRATE_OBJECT, 1)
            ->andReturn($user);

        $this->repoMap['User']
            ->shouldReceive('fetchByLoginId')
            ->once()
            ->with($data['loginId'])
            ->andReturn([m::mock(UserEntity::class)]);

        $this->sut->handleCommand($command);
    }
}
