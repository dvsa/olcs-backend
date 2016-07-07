<?php

namespace Dvsa\OlcsTest\Api\Entity\ContactDetails;

use Mockery as m;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as Entity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\ContactDetails\PhoneContact;
use Dvsa\Olcs\Api\Entity\Person\Person;

/**
 * ContactDetails Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class ContactDetailsEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testConstruct()
    {
        $contactType = m::mock(RefData::class);

        $entity = new ContactDetails($contactType);

        $this->assertSame($contactType, $entity->getContactType());
    }

    public function testCreateForIrfoOperator()
    {
        $data = [
            'emailAddress' => 'test1@test.me',
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
        ];

        $contactType = m::mock(RefData::class)->makePartial();
        $contactType->setId(ContactDetails::CONTACT_TYPE_IRFO_OPERATOR);

        $entity = ContactDetails::create($contactType, $data);

        $this->assertSame($contactType, $entity->getContactType());
        $this->assertEquals($data['emailAddress'], $entity->getEmailAddress());

        $this->assertInstanceOf(Address::class, $entity->getAddress());
        $this->assertEquals($data['address']['addressLine1'], $entity->getAddress()->getAddressLine1());
        $this->assertEquals($data['address']['addressLine2'], $entity->getAddress()->getAddressLine2());
        $this->assertEquals($data['address']['addressLine3'], $entity->getAddress()->getAddressLine3());
        $this->assertEquals($data['address']['addressLine4'], $entity->getAddress()->getAddressLine4());
        $this->assertEquals($data['address']['town'], $entity->getAddress()->getTown());
        $this->assertEquals($data['address']['postcode'], $entity->getAddress()->getPostcode());
        $this->assertEquals($data['address']['countryCode'], $entity->getAddress()->getCountryCode());

        $this->assertEquals(2, count($entity->getPhoneContacts()));
    }

    public function testUpdateForIrfoOperator()
    {
        $data = [
            'emailAddress' => 'updated@test.me',
            'address' => [
                'addressLine1' => 'updated a1',
                'addressLine2' => 'updated a2',
                'addressLine3' => 'updated a3',
                'addressLine4' => 'updated a4',
                'town' => 'updated town',
                'postcode' => 'LS1 2AB',
                'countryCode' => m::mock(Country::class),
            ],
            'phoneContacts' => [
                [
                    'phoneContactType' => m::mock(RefData::class),
                    'phoneNumber' => 'updated pn1',
                ],
                [
                    'id' => 302,
                    'phoneContactType' => m::mock(RefData::class),
                    'phoneNumber' => 'updated pn2',
                ],
                [
                    'phoneContactType' => m::mock(RefData::class),
                    'phoneNumber' => '',
                ],
            ],
        ];

        $contactType = m::mock(RefData::class)->makePartial();
        $contactType->setId(ContactDetails::CONTACT_TYPE_IRFO_OPERATOR);

        $entity = new ContactDetails($contactType);

        // set existing data on the entity before update
        $entity->setEmailAddress('existing@test.me');

        $addressEntity = new Address();
        $addressEntity->setId(200);
        $addressEntity->setAddressLine1('existing a1');
        $addressEntity->setAddressLine2('existing a2');
        $addressEntity->setAddressLine3('existing a3');
        $addressEntity->setAddressLine4('existing a4');
        $addressEntity->setTown('existing town');
        $addressEntity->setPostcode('LS2 9AA');
        $entity->setAddress($addressEntity);

        $phoneContactType = m::mock(RefData::class)->makePartial();

        $phoneContact1Entity = new PhoneContact($phoneContactType);
        $phoneContact1Entity->setId(301);
        $entity->addPhoneContacts($phoneContact1Entity);

        $phoneContact2Entity = new PhoneContact($phoneContactType);
        $phoneContact2Entity->setId(302);
        $entity->addPhoneContacts($phoneContact2Entity);

        $phoneContact3Entity = new PhoneContact($phoneContactType);
        $phoneContact3Entity->setId(303);
        $entity->addPhoneContacts($phoneContact3Entity);

        // update the entity
        $entity->update($data);

        $this->assertSame($contactType, $entity->getContactType());
        $this->assertEquals($data['emailAddress'], $entity->getEmailAddress());

        $this->assertInstanceOf(Address::class, $entity->getAddress());
        $this->assertEquals(200, $entity->getAddress()->getId());
        $this->assertEquals($data['address']['addressLine1'], $entity->getAddress()->getAddressLine1());
        $this->assertEquals($data['address']['addressLine2'], $entity->getAddress()->getAddressLine2());
        $this->assertEquals($data['address']['addressLine3'], $entity->getAddress()->getAddressLine3());
        $this->assertEquals($data['address']['addressLine4'], $entity->getAddress()->getAddressLine4());
        $this->assertEquals($data['address']['town'], $entity->getAddress()->getTown());
        $this->assertEquals($data['address']['postcode'], $entity->getAddress()->getPostcode());
        $this->assertEquals($data['address']['countryCode'], $entity->getAddress()->getCountryCode());

        $phoneContacts = $entity->getPhoneContacts()->toArray();
        $this->assertEquals(2, count($phoneContacts));

        $this->assertEquals(
            $data['phoneContacts'][0]['phoneNumber'],
            $entity->getPhoneContacts()->first()->getPhoneNumber()
        );

        $this->assertEquals(
            $data['phoneContacts'][1]['phoneNumber'],
            $entity->getPhoneContacts()->last()->getPhoneNumber()
        );
    }

    public function testCreateForPartner()
    {
        $data = [
            'description' => 'description',
            'address' => [
                'addressLine1' => 'a12',
                'addressLine2' => 'a23',
                'addressLine3' => 'a34',
                'addressLine4' => 'a45',
                'town' => 'town',
                'postcode' => 'LS1 2AB',
                'countryCode' => m::mock(Country::class),
            ],
        ];

        $contactType = m::mock(RefData::class)->makePartial();
        $contactType->setId(ContactDetails::CONTACT_TYPE_PARTNER);

        $entity = ContactDetails::create($contactType, $data);

        $this->assertSame($contactType, $entity->getContactType());
        $this->assertEquals($data['description'], $entity->getDescription());

        $this->assertInstanceOf(Address::class, $entity->getAddress());
        $this->assertEquals($data['address']['addressLine1'], $entity->getAddress()->getAddressLine1());
        $this->assertEquals($data['address']['addressLine2'], $entity->getAddress()->getAddressLine2());
        $this->assertEquals($data['address']['addressLine3'], $entity->getAddress()->getAddressLine3());
        $this->assertEquals($data['address']['addressLine4'], $entity->getAddress()->getAddressLine4());
        $this->assertEquals($data['address']['town'], $entity->getAddress()->getTown());
        $this->assertEquals($data['address']['postcode'], $entity->getAddress()->getPostcode());
        $this->assertEquals($data['address']['countryCode'], $entity->getAddress()->getCountryCode());

        $this->assertEquals(0, $entity->getPhoneContacts()->count());
    }

    public function testUpdateForPartner()
    {
        $data = [
            'description' => 'updated description',
            'address' => [
                'addressLine1' => 'updated a1',
                'addressLine2' => 'updated a2',
                'addressLine3' => 'updated a3',
                'addressLine4' => 'updated a4',
                'town' => 'updated town',
                'postcode' => 'LS1 2AB',
                'countryCode' => m::mock(Country::class),
            ],
        ];

        $contactType = m::mock(RefData::class)->makePartial();
        $contactType->setId(ContactDetails::CONTACT_TYPE_PARTNER);

        $entity = new ContactDetails($contactType);

        // set existing data on the entity before update
        $entity->setDescription('existing description');

        $addressEntity = new Address();
        $addressEntity->setId(200);
        $addressEntity->setAddressLine1('existing a1');
        $addressEntity->setAddressLine2('existing a2');
        $addressEntity->setAddressLine3('existing a3');
        $addressEntity->setAddressLine4('existing a4');
        $addressEntity->setTown('existing town');
        $addressEntity->setPostcode('LS2 9AA');
        $entity->setAddress($addressEntity);

        // update the entity
        $entity->update($data);

        $this->assertSame($contactType, $entity->getContactType());
        $this->assertEquals($data['description'], $entity->getDescription());

        $this->assertInstanceOf(Address::class, $entity->getAddress());
        $this->assertEquals(200, $entity->getAddress()->getId());
        $this->assertEquals($data['address']['addressLine1'], $entity->getAddress()->getAddressLine1());
        $this->assertEquals($data['address']['addressLine2'], $entity->getAddress()->getAddressLine2());
        $this->assertEquals($data['address']['addressLine3'], $entity->getAddress()->getAddressLine3());
        $this->assertEquals($data['address']['addressLine4'], $entity->getAddress()->getAddressLine4());
        $this->assertEquals($data['address']['town'], $entity->getAddress()->getTown());
        $this->assertEquals($data['address']['postcode'], $entity->getAddress()->getPostcode());
        $this->assertEquals($data['address']['countryCode'], $entity->getAddress()->getCountryCode());

        $this->assertEquals(0, $entity->getPhoneContacts()->count());
    }

    public function testCreateForUser()
    {
        $data = [
            'emailAddress' => 'test1@test.me',
            'person' => [
                'title' => m::mock(RefData::class),
                'forename' => 'forename',
                'familyName' => 'familyName',
                'birthDate' => '1960-02-01',
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
        ];

        $contactType = m::mock(RefData::class)->makePartial();
        $contactType->setId(ContactDetails::CONTACT_TYPE_USER);

        $entity = ContactDetails::create($contactType, $data);

        $this->assertSame($contactType, $entity->getContactType());
        $this->assertEquals($data['emailAddress'], $entity->getEmailAddress());

        $this->assertInstanceOf(Person::class, $entity->getPerson());
        $this->assertEquals($data['person']['forename'], $entity->getPerson()->getForename());
        $this->assertEquals($data['person']['familyName'], $entity->getPerson()->getFamilyName());
        $this->assertEquals($data['person']['birthDate'], $entity->getPerson()->getBirthDate()->format('Y-m-d'));

        $this->assertInstanceOf(Address::class, $entity->getAddress());
        $this->assertEquals($data['address']['addressLine1'], $entity->getAddress()->getAddressLine1());
        $this->assertEquals($data['address']['addressLine2'], $entity->getAddress()->getAddressLine2());
        $this->assertEquals($data['address']['addressLine3'], $entity->getAddress()->getAddressLine3());
        $this->assertEquals($data['address']['addressLine4'], $entity->getAddress()->getAddressLine4());
        $this->assertEquals($data['address']['town'], $entity->getAddress()->getTown());
        $this->assertEquals($data['address']['postcode'], $entity->getAddress()->getPostcode());
        $this->assertEquals($data['address']['countryCode'], $entity->getAddress()->getCountryCode());

        $this->assertEquals(2, count($entity->getPhoneContacts()));
    }

    public function testUpdateForUser()
    {
        $data = [
            'emailAddress' => 'updated@test.me',
            'person' => [
                'title' => m::mock(RefData::class),
                'forename' => 'updated forename',
                'familyName' => 'updated familyName',
                'birthDate' => '1975-12-12',
            ],
            'address' => [
                'addressLine1' => 'updated a1',
                'addressLine2' => 'updated a2',
                'addressLine3' => 'updated a3',
                'addressLine4' => 'updated a4',
                'town' => 'updated town',
                'postcode' => 'LS1 2AB',
                'countryCode' => '',
            ],
            'phoneContacts' => [
                [
                    'phoneContactType' => m::mock(RefData::class),
                    'phoneNumber' => 'updated pn1',
                ],
                [
                    'id' => 302,
                    'phoneContactType' => m::mock(RefData::class),
                    'phoneNumber' => 'updated pn2',
                ],
                [
                    'phoneContactType' => m::mock(RefData::class),
                    'phoneNumber' => '',
                ],
            ],
        ];

        $contactType = m::mock(RefData::class)->makePartial();
        $contactType->setId(ContactDetails::CONTACT_TYPE_USER);

        $entity = new ContactDetails($contactType);

        // set existing data on the entity before update
        $entity->setEmailAddress('existing@test.me');

        $personEntity = new Person();
        $personEntity->setId(100);
        $personEntity->setForename('existing forename');
        $personEntity->setFamilyName('existing familyName');
        $personEntity->setBirthDate(new \DateTime('1960-02-01'));
        $entity->setPerson($personEntity);

        $addressEntity = new Address();
        $addressEntity->setId(200);
        $addressEntity->setAddressLine1('existing a1');
        $addressEntity->setAddressLine2('existing a2');
        $addressEntity->setAddressLine3('existing a3');
        $addressEntity->setAddressLine4('existing a4');
        $addressEntity->setTown('existing town');
        $addressEntity->setPostcode('LS2 9AA');
        $addressEntity->setCountryCode(m::mock(Country::class));
        $entity->setAddress($addressEntity);

        $phoneContactType = m::mock(RefData::class)->makePartial();

        $phoneContact1Entity = new PhoneContact($phoneContactType);
        $phoneContact1Entity->setId(301);
        $entity->addPhoneContacts($phoneContact1Entity);

        $phoneContact2Entity = new PhoneContact($phoneContactType);
        $phoneContact2Entity->setId(302);
        $entity->addPhoneContacts($phoneContact2Entity);

        $phoneContact3Entity = new PhoneContact($phoneContactType);
        $phoneContact3Entity->setId(303);
        $entity->addPhoneContacts($phoneContact3Entity);

        // update the entity
        $entity->update($data);

        $this->assertSame($contactType, $entity->getContactType());
        $this->assertEquals($data['emailAddress'], $entity->getEmailAddress());

        $this->assertInstanceOf(Person::class, $entity->getPerson());
        $this->assertEquals(100, $entity->getPerson()->getId());
        $this->assertEquals($data['person']['forename'], $entity->getPerson()->getForename());
        $this->assertEquals($data['person']['familyName'], $entity->getPerson()->getFamilyName());
        $this->assertEquals($data['person']['birthDate'], $entity->getPerson()->getBirthDate()->format('Y-m-d'));

        $this->assertInstanceOf(Address::class, $entity->getAddress());
        $this->assertEquals(200, $entity->getAddress()->getId());
        $this->assertEquals($data['address']['addressLine1'], $entity->getAddress()->getAddressLine1());
        $this->assertEquals($data['address']['addressLine2'], $entity->getAddress()->getAddressLine2());
        $this->assertEquals($data['address']['addressLine3'], $entity->getAddress()->getAddressLine3());
        $this->assertEquals($data['address']['addressLine4'], $entity->getAddress()->getAddressLine4());
        $this->assertEquals($data['address']['town'], $entity->getAddress()->getTown());
        $this->assertEquals($data['address']['postcode'], $entity->getAddress()->getPostcode());
        $this->assertNull($entity->getAddress()->getCountryCode());

        $phoneContacts = $entity->getPhoneContacts()->toArray();
        $this->assertEquals(2, count($phoneContacts));

        $this->assertEquals(
            $data['phoneContacts'][0]['phoneNumber'],
            $entity->getPhoneContacts()->first()->getPhoneNumber()
        );

        $this->assertEquals(
            $data['phoneContacts'][1]['phoneNumber'],
            $entity->getPhoneContacts()->last()->getPhoneNumber()
        );
    }

    public function testUpdateContactDetailsWithPersonAndEmailAddress()
    {
        $contactType = m::mock(RefData::class);
        $entity = new ContactDetails($contactType);
        $person = m::mock(PersonEntity::class);

        $entity->updateContactDetailsWithPersonAndEmailAddress($person, 'email@address.com');
        $this->assertSame($person, $entity->getPerson());
        $this->assertEquals('email@address.com', $entity->getEmailAddress());
    }

    public function testCreateForCorrespondenceAddress()
    {
        $data = [
            'emailAddress' => 'test1@test.me',
            'address' => [
                'addressLine1' => 'a12',
                'addressLine2' => 'a23',
                'addressLine3' => 'a34',
                'addressLine4' => 'a45',
                'town' => 'town',
                'postcode' => 'LS1 2AB',
                'countryCode' => m::mock(Country::class),
            ],
            'businessPhoneContact' => [
                'phoneContactType' => m::mock(RefData::class),
                'phoneNumber' => '111',
            ],
            'homePhoneContact' => [
                'phoneContactType' => m::mock(RefData::class),
                'phoneNumber' => '222',
            ],
        ];

        $contactType = m::mock(RefData::class)->makePartial();
        $contactType->setId(ContactDetails::CONTACT_TYPE_CORRESPONDENCE_ADDRESS);

        $entity = ContactDetails::create($contactType, $data);

        $this->assertSame($contactType, $entity->getContactType());
        $this->assertEquals($data['emailAddress'], $entity->getEmailAddress());

        $this->assertInstanceOf(Address::class, $entity->getAddress());
        $this->assertEquals($data['address']['addressLine1'], $entity->getAddress()->getAddressLine1());
        $this->assertEquals($data['address']['addressLine2'], $entity->getAddress()->getAddressLine2());
        $this->assertEquals($data['address']['addressLine3'], $entity->getAddress()->getAddressLine3());
        $this->assertEquals($data['address']['addressLine4'], $entity->getAddress()->getAddressLine4());
        $this->assertEquals($data['address']['town'], $entity->getAddress()->getTown());
        $this->assertEquals($data['address']['postcode'], $entity->getAddress()->getPostcode());
        $this->assertEquals($data['address']['countryCode'], $entity->getAddress()->getCountryCode());

        $this->assertEquals(2, count($entity->getPhoneContacts()));
    }

    public function testUpdateForCorrespondenceAddress()
    {
        $data = [
            'emailAddress' => 'updated@test.me',
            'address' => [
                'addressLine1' => 'updated a1',
                'addressLine2' => 'updated a2',
                'addressLine3' => 'updated a3',
                'addressLine4' => 'updated a4',
                'town' => 'updated town',
                'postcode' => 'LS1 2AB',
                'countryCode' => m::mock(Country::class),
            ],
            'businessPhoneContact' => [
                'phoneContactType' => m::mock(RefData::class),
                'phoneNumber' => 'updated pn1',
            ],
            'homePhoneContact' => [
                'id' => 302,
                'phoneContactType' => m::mock(RefData::class),
                'phoneNumber' => 'updated pn2',
            ],
            'faxPhoneContact' => [
                'phoneContactType' => m::mock(RefData::class),
                'phoneNumber' => '',
            ],
        ];

        $contactType = m::mock(RefData::class)->makePartial();
        $contactType->setId(ContactDetails::CONTACT_TYPE_CORRESPONDENCE_ADDRESS);

        $entity = new ContactDetails($contactType);

        // set existing data on the entity before update
        $entity->setEmailAddress('existing@test.me');

        $addressEntity = new Address();
        $addressEntity->setId(200);
        $addressEntity->setAddressLine1('existing a1');
        $addressEntity->setAddressLine2('existing a2');
        $addressEntity->setAddressLine3('existing a3');
        $addressEntity->setAddressLine4('existing a4');
        $addressEntity->setTown('existing town');
        $addressEntity->setPostcode('LS2 9AA');
        $entity->setAddress($addressEntity);

        $phoneContactType = m::mock(RefData::class)->makePartial();

        $phoneContact1Entity = new PhoneContact($phoneContactType);
        $phoneContact1Entity->setId(301);
        $entity->addPhoneContacts($phoneContact1Entity);

        $phoneContact2Entity = new PhoneContact($phoneContactType);
        $phoneContact2Entity->setId(302);
        $entity->addPhoneContacts($phoneContact2Entity);

        $phoneContact3Entity = new PhoneContact($phoneContactType);
        $phoneContact3Entity->setId(303);
        $entity->addPhoneContacts($phoneContact3Entity);

        // update the entity
        $entity->update($data);

        $this->assertSame($contactType, $entity->getContactType());
        $this->assertEquals($data['emailAddress'], $entity->getEmailAddress());

        $this->assertInstanceOf(Address::class, $entity->getAddress());
        $this->assertEquals(200, $entity->getAddress()->getId());
        $this->assertEquals($data['address']['addressLine1'], $entity->getAddress()->getAddressLine1());
        $this->assertEquals($data['address']['addressLine2'], $entity->getAddress()->getAddressLine2());
        $this->assertEquals($data['address']['addressLine3'], $entity->getAddress()->getAddressLine3());
        $this->assertEquals($data['address']['addressLine4'], $entity->getAddress()->getAddressLine4());
        $this->assertEquals($data['address']['town'], $entity->getAddress()->getTown());
        $this->assertEquals($data['address']['postcode'], $entity->getAddress()->getPostcode());
        $this->assertEquals($data['address']['countryCode'], $entity->getAddress()->getCountryCode());

        $phoneContacts = $entity->getPhoneContacts()->toArray();
        $this->assertEquals(2, count($phoneContacts));

        $this->assertEquals(
            $data['businessPhoneContact']['phoneNumber'],
            $entity->getPhoneContacts()->first()->getPhoneNumber()
        );

        $this->assertEquals(
            $data['homePhoneContact']['phoneNumber'],
            $entity->getPhoneContacts()->last()->getPhoneNumber()
        );
    }
}
