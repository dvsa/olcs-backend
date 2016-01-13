<?php

/**
 * Save Addresses test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\SaveAddresses;

use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails;
use Dvsa\Olcs\Api\Domain\Repository\PhoneContact;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\PhoneContact as PhoneContactEntity;

use Dvsa\Olcs\Api\Domain\Command\Licence\SaveAddresses as Cmd;
use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress;

/**
 * Save Addresses test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class SaveAddressesTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new SaveAddresses();
        $this->mockRepo('Licence', Licence::class);
        $this->mockRepo('ContactDetails', ContactDetails::class);
        $this->mockRepo('PhoneContact', PhoneContact::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'phone_t_tel',
            'phone_t_home',
            'phone_t_mobile',
            'phone_t_fax'
        ];

        $this->references = [
            ContactDetailsEntity::class => [
                50 => m::mock(ContactDetailsEntity::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithFullyPopulatedNewData()
    {
        $data = [
            'id' => 10,
            'correspondence' => [
                'id' => '',
                'version' => '',
                'fao' => 'foo bar'
            ],
            'correspondenceAddress' => [
                'id' => '',
                'version' => '',
                'addressLine1' => 'Address 1',
                'town' => 'Leeds',
                'postcode' => 'LS9 6NF',
                'countryCode' => 'GB',
            ],
            'contact' => [
                'phone_business' => '01131231234',
                'phone_business_id' => '',
                'phone_business_version' => '',

                'phone_home' => '01131231234',
                'phone_home_id' => '',
                'phone_home_version' => '',

                'phone_mobile' => '01131231234',
                'phone_mobile_id' => '',
                'phone_mobile_version' => '',

                'phone_fax' => '01131231234',
                'phone_fax_id' => '',
                'phone_fax_version' => '',

                'email' => 'contact@email.com'
            ],
            'establishment' => [
                'id' => '',
                'version' => ''
            ],
            'establishmentAddress' => [
                'id' => '',
                'version' => '',
                'addressLine1' => 'Est Address 1',
                'town' => 'Est Leeds',
                'postcode' => 'LS8 5NF',
                'countryCode' => 'GB',
            ],
            'consultant' => [
                'add-transport-consultant' => 'Y',
                'writtenPermissionToEngage' => 'Y',
                'transportConsultantName' => 'A TC',
                'address' => [
                    'id' => '',
                    'version' => '',
                    'addressLine1' => 'Con Address 1',
                    'town' => 'Con Leeds',
                    'postcode' => 'LS7 4NF',
                    'countryCode' => 'GB',
                ],
                'contact' => [
                    'phone_business' => '01131231234',
                    'phone_business_id' => '',
                    'phone_business_version' => '',

                    'phone_home' => '01131231234',
                    'phone_home_id' => '',
                    'phone_home_version' => '',

                    'phone_mobile' => '01131231234',
                    'phone_mobile_id' => '',
                    'phone_mobile_version' => '',

                    'phone_fax' => '01131231234',
                    'phone_fax_id' => '',
                    'phone_fax_version' => '',

                    'email' => 'tc@email.com'
                ]
            ]
        ];

        $command = Cmd::create($data);

        $correspondenceCd = m::mock(ContactDetailsEntity::class)
            ->shouldReceive('setFao')
            ->with('foo bar')
            ->shouldReceive('setEmailAddress')
            ->with('contact@email.com')
            ->shouldReceive('getVersion')
            ->andReturn(null, 1)
            ->shouldReceive('getPhoneContacts')
            ->andReturn(
                m::mock()
                ->shouldReceive('add')
                ->times(4)
                ->getMock()
            )
            ->getMock();

        $transportConsultantCd = m::mock(ContactDetailsEntity::class)
            ->shouldReceive('setFao')
            ->with('A TC')
            ->shouldReceive('setEmailAddress')
            ->with('tc@email.com')
            ->shouldReceive('setWrittenPermissionToEngage')
            ->with('Y')
            ->shouldReceive('getVersion')
            ->andReturn(null, 1)
            ->shouldReceive('getPhoneContacts')
            ->andReturn(
                m::mock()
                ->shouldReceive('add')
                ->times(4)
                ->getMock()
            )
            ->getMock();

        $licence = m::mock(LicenceEntity::class)
            ->makePartial()
            ->shouldReceive('setCorrespondenceCd')
            ->shouldReceive('getCorrespondenceCd')
            ->andReturn($correspondenceCd)
            ->shouldReceive('getTransportConsultantCd')
            ->andReturn($transportConsultantCd)
            ->getMock();

        $result = new Result();

        $result->setFlag('hasChanged', true);
        $result->addId('contactDetails', 50);

        $this->expectedSideEffect(
            SaveAddress::class,
            [
                'id' => '',
                'version' => '',
                'addressLine1' => 'Address 1',
                'addressLine2' => null,
                'addressLine3' => null,
                'addressLine4' => null,
                'town' => 'Leeds',
                'postcode' => 'LS9 6NF',
                'countryCode' => 'GB',
                'contactType' => 'ct_corr'
            ],
            $result
        );

        $result = new Result();

        $result->setFlag('hasChanged', true);
        $result->addId('contactDetails', 51);

        $this->expectedSideEffect(
            SaveAddress::class,
            [
                'id' => '',
                'version' => '',
                'addressLine1' => 'Est Address 1',
                'addressLine2' => null,
                'addressLine3' => null,
                'addressLine4' => null,
                'town' => 'Est Leeds',
                'postcode' => 'LS8 5NF',
                'countryCode' => 'GB',
                'contactType' => 'ct_est'
            ],
            $result
        );

        $result = new Result();

        $result->setFlag('hasChanged', true);
        $result->addId('contactDetails', 52);

        $this->expectedSideEffect(
            SaveAddress::class,
            [
                'id' => '',
                'version' => '',
                'addressLine1' => 'Con Address 1',
                'addressLine2' => null,
                'addressLine3' => null,
                'addressLine4' => null,
                'town' => 'Con Leeds',
                'postcode' => 'LS7 4NF',
                'countryCode' => 'GB',
                'contactType' => 'ct_tcon'
            ],
            $result
        );

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence)
            ->once()
            ->shouldReceive('save')
            ->with($licence)
            ->once()
            ->getMock();

        $this->repoMap['ContactDetails']->shouldReceive('save')
            ->with($correspondenceCd)
            ->shouldReceive('save')
            ->with($transportConsultantCd);

        $this->repoMap['PhoneContact']->shouldReceive('save')
            // correspondence phone contact +
            // transport consultant phone contacts
            ->times(8);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                // @NOTE: this index gets overridden...
                'contactDetails' => 52
            ],
            'messages' => [
                'Contact details updated',
                'Phone contact business created',
                'Phone contact home created',
                'Phone contact mobile created',
                'Phone contact fax created',
                'Phone contact business created',
                'Phone contact home created',
                'Phone contact mobile created',
                'Phone contact fax created',
                'Transport consultant updated',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleExistingCorrespondenceUpdateWithNoChangeAndDifferentPhoneNumbers()
    {
        $data = [
            'id' => 10,
            'correspondence' => [
                'id' => '',
                'version' => '',
                'fao' => 'foo bar'
            ],
            'correspondenceAddress' => [
                'id' => '',
                'version' => '',
                'addressLine1' => 'Address 1',
                'town' => 'Leeds',
                'postcode' => 'LS9 6NF',
                'countryCode' => 'GB',
            ],
            'contact' => [
                'phone_business' => '01131231234',
                'phone_business_id' => '1',
                'phone_business_version' => '1',

                'phone_home' => '01131231234',
                'phone_home_id' => '2',
                'phone_home_version' => '1',

                'phone_mobile' => '01131231234',
                'phone_mobile_id' => '3',
                'phone_mobile_version' => '1',

                'phone_fax' => '01131231234',
                'phone_fax_id' => '4',
                'phone_fax_version' => '1',

                'email' => 'contact@email.com'
            ]
        ];

        $command = Cmd::create($data);

        $correspondenceCd = m::mock(ContactDetailsEntity::class)
            ->shouldReceive('setFao')
            ->with('foo bar')
            ->shouldReceive('setEmailAddress')
            ->with('contact@email.com')
            ->shouldReceive('getVersion')
            ->andReturn(1)
            ->getMock();

        $licence = m::mock(LicenceEntity::class)
            ->makePartial()
            ->shouldReceive('getCorrespondenceCd')
            ->andReturn($correspondenceCd)
            ->getMock();

        $result = new Result();

        $result->setFlag('hasChanged', false);

        $this->expectedSideEffect(
            SaveAddress::class,
            [
                'id' => '',
                'version' => '',
                'addressLine1' => 'Address 1',
                'addressLine2' => null,
                'addressLine3' => null,
                'addressLine4' => null,
                'town' => 'Leeds',
                'postcode' => 'LS9 6NF',
                'countryCode' => 'GB',
                'contactType' => 'ct_corr'
            ],
            $result
        );

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence)
            ->once()
            ->shouldReceive('save')
            ->with($licence)
            ->once()
            ->getMock();

        $this->repoMap['ContactDetails']->shouldReceive('save')
            ->with($correspondenceCd);

        $this->repoMap['PhoneContact']->shouldReceive('save')
            ->times(4)
            ->shouldReceive('fetchById')
            ->with(1, 1, 1)
            ->andReturn(
                m::mock(PhoneContactEntity::class)
                ->makePartial()
                ->shouldReceive('setContactDetails')
                ->with($correspondenceCd)
                ->shouldReceive('getVersion')
                ->andReturn(2)
                ->getMock()
            )
            ->shouldReceive('fetchById')
            ->with(2, 1, 1)
            ->andReturn(
                m::mock(PhoneContactEntity::class)
                ->makePartial()
                ->shouldReceive('setContactDetails')
                ->with($correspondenceCd)
                ->shouldReceive('getVersion')
                ->andReturn(2)
                ->getMock()
            )
            ->shouldReceive('fetchById')
            ->with(3, 1, 1)
            ->andReturn(
                m::mock(PhoneContactEntity::class)
                ->makePartial()
                ->shouldReceive('setContactDetails')
                ->with($correspondenceCd)
                ->shouldReceive('getVersion')
                ->andReturn(2)
                ->getMock()
            )
            ->shouldReceive('fetchById')
            ->with(4, 1, 1)
            ->andReturn(
                m::mock(PhoneContactEntity::class)
                ->makePartial()
                ->shouldReceive('setContactDetails')
                ->with($correspondenceCd)
                ->shouldReceive('getVersion')
                ->andReturn(2)
                ->getMock()
            )
            ->getMock();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Phone contact business updated',
                'Phone contact home updated',
                'Phone contact mobile updated',
                'Phone contact fax updated',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleExistingCorrespondenceUpdateWithNoChangeAndDeletedPhoneNumbers()
    {
        $data = [
            'id' => 10,
            'correspondence' => [
                'id' => '',
                'version' => '',
                'fao' => 'foo bar'
            ],
            'correspondenceAddress' => [
                'id' => '',
                'version' => '',
                'addressLine1' => 'Address 1',
                'town' => 'Leeds',
                'postcode' => 'LS9 6NF',
                'countryCode' => 'GB',
            ],
            'contact' => [
                'phone_business' => '',
                'phone_business_id' => '1',
                'phone_business_version' => '1',

                'phone_home' => '',
                'phone_home_id' => '2',
                'phone_home_version' => '1',

                'phone_mobile' => '',
                'phone_mobile_id' => '3',
                'phone_mobile_version' => '1',

                'phone_fax' => '',
                'phone_fax_id' => '4',
                'phone_fax_version' => '1',

                'email' => 'contact@email.com'
            ]
        ];

        $command = Cmd::create($data);

        $correspondenceCd = m::mock(ContactDetailsEntity::class)
            ->shouldReceive('setFao')
            ->with('foo bar')
            ->shouldReceive('setEmailAddress')
            ->with('contact@email.com')
            ->shouldReceive('getVersion')
            ->andReturn(1)
            ->shouldReceive('getPhoneContacts')
            ->andReturn(
                m::mock()
                ->shouldReceive('removeElement')
                ->times(4)
                ->getMock()
            )
            ->getMock();

        $licence = m::mock(LicenceEntity::class)
            ->makePartial()
            ->shouldReceive('getCorrespondenceCd')
            ->andReturn($correspondenceCd)
            ->getMock();

        $result = new Result();

        $result->setFlag('hasChanged', false);

        $this->expectedSideEffect(
            SaveAddress::class,
            [
                'id' => '',
                'version' => '',
                'addressLine1' => 'Address 1',
                'addressLine2' => null,
                'addressLine3' => null,
                'addressLine4' => null,
                'town' => 'Leeds',
                'postcode' => 'LS9 6NF',
                'countryCode' => 'GB',
                'contactType' => 'ct_corr'
            ],
            $result
        );

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence)
            ->once()
            ->shouldReceive('save')
            ->with($licence)
            ->once()
            ->getMock();

        $this->repoMap['ContactDetails']->shouldReceive('save')
            ->with($correspondenceCd);

        $this->repoMap['PhoneContact']->shouldReceive('delete')
            ->times(4)
            ->shouldReceive('fetchById')
            ->with(1, 1, 1)
            ->andReturn(
                m::mock(PhoneContactEntity::class)
                ->makePartial()
                ->shouldReceive('setContactDetails')
                ->with($correspondenceCd)
                ->shouldReceive('getId')
                ->andReturn(1)
                ->getMock()
            )
            ->shouldReceive('fetchById')
            ->with(2, 1, 1)
            ->andReturn(
                m::mock(PhoneContactEntity::class)
                ->makePartial()
                ->shouldReceive('setContactDetails')
                ->with($correspondenceCd)
                ->shouldReceive('getId')
                ->andReturn(2)
                ->getMock()
            )
            ->shouldReceive('fetchById')
            ->with(3, 1, 1)
            ->andReturn(
                m::mock(PhoneContactEntity::class)
                ->makePartial()
                ->shouldReceive('setContactDetails')
                ->with($correspondenceCd)
                ->shouldReceive('getId')
                ->andReturn(3)
                ->getMock()
            )
            ->shouldReceive('fetchById')
            ->with(4, 1, 1)
            ->andReturn(
                m::mock(PhoneContactEntity::class)
                ->makePartial()
                ->shouldReceive('setContactDetails')
                ->with($correspondenceCd)
                ->shouldReceive('getId')
                ->andReturn(4)
                ->getMock()
            )
            ->getMock();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Phone contact business deleted',
                'Phone contact home deleted',
                'Phone contact mobile deleted',
                'Phone contact fax deleted',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleExistingCorrespondenceUpdateWithNoChangeAndDeletedTransportConsultant()
    {
        $data = [
            'id' => 10,
            'correspondence' => [
                'id' => '',
                'version' => '',
                'fao' => 'foo bar'
            ],
            'correspondenceAddress' => [
                'id' => '',
                'version' => '',
                'addressLine1' => 'Address 1',
                'town' => 'Leeds',
                'postcode' => 'LS9 6NF',
                'countryCode' => 'GB',
            ],
            'contact' => [
                'phone_business' => '01131231234',
                'phone_business_id' => '1',
                'phone_business_version' => '1',

                'phone_home' => '01131231234',
                'phone_home_id' => '2',
                'phone_home_version' => '1',

                'phone_mobile' => '01131231234',
                'phone_mobile_id' => '3',
                'phone_mobile_version' => '1',

                'phone_fax' => '01131231234',
                'phone_fax_id' => '4',
                'phone_fax_version' => '1',

                'email' => 'contact@email.com'
            ],
            'consultant' => [
                'add-transport-consultant' => 'N'
            ]
        ];

        $command = Cmd::create($data);

        $correspondenceCd = m::mock(ContactDetailsEntity::class)
            ->shouldReceive('setFao')
            ->with('foo bar')
            ->shouldReceive('setEmailAddress')
            ->with('contact@email.com')
            ->shouldReceive('getVersion')
            ->andReturn(1)
            ->getMock();

        $licence = m::mock(LicenceEntity::class)
            ->makePartial()
            ->shouldReceive('getCorrespondenceCd')
            ->andReturn($correspondenceCd)
            ->shouldReceive('getTransportConsultantCd')
            ->andReturn(
                m::mock(ContactDetailsEntity::class)
            )
            ->shouldReceive('setTransportConsultantCd')
            ->with(null)
            ->getMock();

        $result = new Result();

        $result->setFlag('hasChanged', false);

        $this->expectedSideEffect(
            SaveAddress::class,
            [
                'id' => '',
                'version' => '',
                'addressLine1' => 'Address 1',
                'addressLine2' => null,
                'addressLine3' => null,
                'addressLine4' => null,
                'town' => 'Leeds',
                'postcode' => 'LS9 6NF',
                'countryCode' => 'GB',
                'contactType' => 'ct_corr'
            ],
            $result
        );

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence)
            ->once()
            ->shouldReceive('save')
            ->with($licence)
            ->once()
            ->getMock();

        $this->repoMap['ContactDetails']->shouldReceive('save')
            ->with($correspondenceCd);

        $this->repoMap['PhoneContact']->shouldReceive('save')
            ->times(4)
            ->shouldReceive('fetchById')
            ->with(1, 1, 1)
            ->andReturn(
                m::mock(PhoneContactEntity::class)
                ->makePartial()
                ->shouldReceive('setContactDetails')
                ->with($correspondenceCd)
                ->getMock()
            )
            ->shouldReceive('fetchById')
            ->with(2, 1, 1)
            ->andReturn(
                m::mock(PhoneContactEntity::class)
                ->makePartial()
                ->shouldReceive('setContactDetails')
                ->with($correspondenceCd)
                ->getMock()
            )
            ->shouldReceive('fetchById')
            ->with(3, 1, 1)
            ->andReturn(
                m::mock(PhoneContactEntity::class)
                ->makePartial()
                ->shouldReceive('setContactDetails')
                ->with($correspondenceCd)
                ->getMock()
            )
            ->shouldReceive('fetchById')
            ->with(4, 1, 1)
            ->andReturn(
                m::mock(PhoneContactEntity::class)
                ->makePartial()
                ->shouldReceive('setContactDetails')
                ->with($correspondenceCd)
                ->getMock()
            )
            ->getMock();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Transport consultant deleted'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
