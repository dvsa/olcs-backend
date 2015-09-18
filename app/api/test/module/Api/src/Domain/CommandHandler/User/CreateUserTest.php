<?php

/**
 * Create User Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\User;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\User\CreateUser as Sut;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\Repository\User;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\User\CreateUser as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Create User Test
 */
class CreateUserTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Sut();
        $this->mockRepo('User', User::class);
        $this->mockRepo('ContactDetails', ContactDetails::class);
        $this->mockRepo('Licence', Licence::class);

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

    public function testHandleCommand()
    {
        $userId = 111;
        $licenceNumber = 'LIC123';

        $data = [
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
                        'phoneContactType' => m::mock(RefData::class),
                        'phoneNumber' => '222',
                    ]
                ],
            ],
        ];

        $command = Cmd::create($data);

        $this->repoMap['User']
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
            UserEntity::USER_TYPE_OPERATOR,
            $savedUser->getUserType()
        );
        $this->assertEquals(
            ContactDetailsEntity::CONTACT_TYPE_USER,
            $savedUser->getContactDetails()->getContactType()->getId()
        );
        $this->assertEquals(
            $data['contactDetails']['emailAddress'],
            $savedUser->getContactDetails()->getEmailAddress()
        );
    }
}
