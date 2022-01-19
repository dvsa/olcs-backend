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
use ZfcRbac\Service\AuthorizationService;

/**
 * Update User Test
 */
class UpdateUserCognitoTest extends CommandHandlerTestCase
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
                'identity_provider' => JWTIdentityProvider::class
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

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testHandleCommandWithCognitoPasswordResetByEmail()
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

        $mockConfig = [
            'auth' => [
                'identity_provider' => JWTIdentityProvider::class
            ]
        ];

        $this->mockedSmServices['Config'] = $mockConfig;

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


        /** @var ContactDetailsEntity $contactDetails */
        $contactDetails = m::mock(ContactDetailsEntity::class)->makePartial();
        $contactDetails->shouldReceive('update')
            ->once()
            ->with($data['contactDetails'])
            ->andReturnSelf();

        /** @var UserEntity $user */
        $user = m::mock(UserEntity::class)->makePartial();
        $user->initCollections();
        $user->setId($userId);
        $user->setPid('pid');
        $user->setLoginId($data['loginId']);
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

        $this->repoMap['User']->shouldReceive('save')
            ->once()
            ->with(m::type(UserEntity::class));

        $this->mockedSmServices[ValidatableAdapterInterface::class]
            ->shouldReceive('resetPassword')
            ->withArgs([$data['loginId'], 'GENERATED_PASSWORD', false]);

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
            ->andReturn('GENERATED_PASSWORD');

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
    public function testHandleCommandWithCognitoPasswordResetByPost()
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

        /** @var ContactDetailsEntity $contactDetails */
        $contactDetails = m::mock(ContactDetailsEntity::class)
            ->makePartial()
            ->shouldReceive('setId');

        $contactDetails->shouldReceive('update')
            ->with($data['contactDetails'])
            ->andReturnSelf();

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

        /** @var UserEntity $user */
        $user = m::mock(UserEntity::class)->makePartial();
        $user->initCollections();
        $user->setId($userId);
        $user->setPid('pid');
        $user->setLoginId($data['loginId']);
        $user->setContactDetails($contactDetails);
        $user->setOrganisationUsers(new ArrayCollection([$orgUser]));
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
            ->andReturn('GENERATED_PASSWORD');

        $this->mockedSmServices[ValidatableAdapterInterface::class]
            ->shouldReceive('resetPassword')
            ->withArgs([$data['loginId'], 'GENERATED_PASSWORD', false]);

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
