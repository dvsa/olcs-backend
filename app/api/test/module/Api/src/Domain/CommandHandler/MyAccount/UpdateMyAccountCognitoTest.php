<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\MyAccount;

use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Rbac\JWTIdentityProvider;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Dvsa\Olcs\Api\Service\OpenAm\UserInterface;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\MyAccount\UpdateMyAccount as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\MyAccount\UpdateMyAccount;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails;
use Dvsa\Olcs\Api\Domain\Repository\User;
use Dvsa\Olcs\Api\Domain\Repository\Person;
use Dvsa\Olcs\Api\Domain\Repository\PhoneContact;
use Dvsa\Olcs\Api\Domain\Repository\Address;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\PhoneContact as PhoneContactEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as AddressEntity;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;
use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;

/**
 * Update MyAccount Test
 */
class UpdateMyAccountCognitoTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new UpdateMyAccount();
        $this->mockRepo('User', User::class);
        $this->mockRepo('ContactDetails', ContactDetails::class);
        $this->mockRepo('PhoneContact', PhoneContact::class);
        $this->mockRepo('Address', Address::class);
        $this->mockRepo('Person', Person::class);

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
            'Config' => $mockConfig
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            ContactDetailsEntity::CONTACT_TYPE_USER,
            PhoneContactEntity::TYPE_PRIMARY,
            'title_mr',
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

    public function testHandleCommand()
    {
        $userId = 1;

        $data = [
            'id' => 111,
            'version' => 1,
            'team' => 1,
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

        /** @var TeamEntity $user */
        $team = m::mock(Team::class)->makePartial();

        $this->mockedSmServices[ValidatableAdapterInterface::class]->shouldReceive('changeAttribute')
            ->with('login_id', 'email', 'test1@test.me')
            ->once();

        $this->mockedSmServices[UserInterface::class]->shouldNotHaveBeenCalled();

        /** @var UserEntity $user */
        $user = m::mock(UserEntity::class)->makePartial();
        $user->setId($userId);
        $user->setLoginId('login_id');
        $user->setTeam($team);
        $user->setPid('some-pid');

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($user);

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

        $this->assertInstanceOf(ContactDetailsEntity::class, $savedUser->getContactDetails());
        $this->assertEquals(
            $data['contactDetails']['emailAddress'],
            $savedUser->getContactDetails()->getEmailAddress()
        );
    }
}
