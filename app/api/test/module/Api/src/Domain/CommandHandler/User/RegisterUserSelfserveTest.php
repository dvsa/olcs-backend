<?php

/**
 * Register User Selfserve Test
 */

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\User;

use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Dvsa\Olcs\Api\Service\OpenAm\UserInterface;
use Dvsa\Olcs\Auth\Service\PasswordService;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Email\SendUserRegistered as SendUserRegisteredDto;
use Dvsa\Olcs\Api\Domain\Command\Email\SendUserTemporaryPassword as SendUserTemporaryPasswordDto;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as EnqueueFileCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\User\RegisterUserSelfserve as Sut;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\Repository\Organisation;
use Dvsa\Olcs\Api\Domain\Repository\User;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Transfer\Command\User\RegisterUserSelfserve as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Register User Selfserve Test
 */
class RegisterUserSelfserveTest extends CommandHandlerTestCase
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
        $this->mockRepo('Licence', Licence::class);
        $this->mockRepo('Organisation', Organisation::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class),
            UserInterface::class => m::mock(UserInterface::class)
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            ContactDetailsEntity::CONTACT_TYPE_USER,
            OrganisationEntity::ORG_TYPE_SOLE_TRADER
        ];

        parent::initReferences();
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testHandleCommandWithOrg()
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
            'organisationName' => 'Org Name',
            'businessType' => OrganisationEntity::ORG_TYPE_SOLE_TRADER,
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

        /** @var OrganisationEntity $savedOrg */
        $savedOrg = null;

        $this->repoMap['Organisation']->shouldReceive('save')
            ->once()
            ->with(m::type(OrganisationEntity::class))
            ->andReturnUsing(
                function (OrganisationEntity $org) use (&$savedOrg) {
                    $savedOrg = $org;
                }
            );

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
                        SendUserRegisteredDto::class,
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
            );

        $this->mockedAdapter->shouldReceive('register')
            ->once();

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

        $this->assertEquals(OrganisationEntity::ORG_TYPE_SOLE_TRADER, $savedOrg->getType()->getId());
        $this->assertEquals($data['organisationName'], $savedOrg->getName());
        $this->assertEquals('Y', $savedOrg->getAllowEmail());

        $this->assertInstanceOf(ContactDetailsEntity::class, $savedUser->getContactDetails());
        $this->assertEquals(
            ContactDetailsEntity::CONTACT_TYPE_USER,
            $savedUser->getContactDetails()->getContactType()->getId()
        );
        $this->assertEquals(
            $data['contactDetails']['emailAddress'],
            $savedUser->getContactDetails()->getEmailAddress()
        );

        $this->assertEquals(UserEntity::USER_TYPE_OPERATOR, $savedUser->getUserType());
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testHandleCommandWithLicence()
    {
        $userId = 111;
        $licId = 222;

        $data = [
            'loginId' => 'login_id',
            'contactDetails' => [
                'emailAddress' => 'test1@test.me',
                'person' => [
                    'forename' => 'updated forename',
                    'familyName' => 'updated familyName',
                ],
            ],
            'licenceNumber' => 'licNo',
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

        $this->mockedAdapter->shouldReceive('register')
            ->once();

        $org = m::mock(OrganisationEntity::class);

        $licence = m::mock(LicenceEntity::class);
        $licence->shouldReceive('getId')->andReturn($licId);
        $licence->shouldReceive('getOrganisation')->andReturn($org);

        $this->repoMap['Licence']->shouldReceive('fetchForUserRegistration')
            ->once()
            ->with($data['licenceNumber'])
            ->andReturn($licence);

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
                    'SELF_SERVICE_PASSWORD' => 'abcdef123456'
                ],
                'description' => 'Self service new password letter',
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

        $this->assertEquals(UserEntity::USER_TYPE_OPERATOR, $savedUser->getUserType());
    }

    public function testHandleCommandThrowsIncorrectOrgException()
    {
        $this->expectException(\Dvsa\Olcs\Api\Domain\Exception\BadRequestException::class);

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
        $data = [
            'loginId' => 'login_id',
            'contactDetails' => [
                'emailAddress' => 'test1@test.me',
                'person' => [
                    'forename' => 'updated forename',
                    'familyName' => 'updated familyName',
                ],
            ],
            'organisationName' => 'Org Name',
            'businessType' => OrganisationEntity::ORG_TYPE_SOLE_TRADER,
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

        $this->repoMap['Organisation']->shouldReceive('save')
            ->once()
            ->with(m::type(OrganisationEntity::class))
            ->andReturnUsing(
                function (OrganisationEntity $org) use (&$savedOrg) {
                    $savedOrg = $org;
                }
            );

        $this->repoMap['User']
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

        $this->repoMap['User']->shouldReceive('delete')->once();

        $this->mockedAdapter->shouldReceive('register')
            ->once()
            ->andThrow(ClientException::class);

        $this->expectException(\Exception::class);

        $this->sut->handleCommand($command);
    }
}
