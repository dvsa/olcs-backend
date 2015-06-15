<?php

namespace Dvsa\OlcsTest\Api\Entity\ContactDetails;

use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as Entity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\ContactDetails\PhoneContact;

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

        $this->assertInstanceOf('Dvsa\Olcs\Api\Entity\ContactDetails\Address', $entity->getAddress());
        $this->assertEquals($data['address']['addressLine1'], $entity->getAddress()->getAddressLine1());
        $this->assertEquals($data['address']['addressLine2'], $entity->getAddress()->getAddressLine2());
        $this->assertEquals($data['address']['addressLine3'], $entity->getAddress()->getAddressLine3());
        $this->assertEquals($data['address']['addressLine4'], $entity->getAddress()->getAddressLine4());
        $this->assertEquals($data['address']['town'], $entity->getAddress()->getTown());
        $this->assertEquals($data['address']['postcode'], $entity->getAddress()->getPostcode());

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

        $this->assertInstanceOf('Dvsa\Olcs\Api\Entity\ContactDetails\Address', $entity->getAddress());
        $this->assertEquals(200, $entity->getAddress()->getId());
        $this->assertEquals($data['address']['addressLine1'], $entity->getAddress()->getAddressLine1());
        $this->assertEquals($data['address']['addressLine2'], $entity->getAddress()->getAddressLine2());
        $this->assertEquals($data['address']['addressLine3'], $entity->getAddress()->getAddressLine3());
        $this->assertEquals($data['address']['addressLine4'], $entity->getAddress()->getAddressLine4());
        $this->assertEquals($data['address']['town'], $entity->getAddress()->getTown());
        $this->assertEquals($data['address']['postcode'], $entity->getAddress()->getPostcode());

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
}
