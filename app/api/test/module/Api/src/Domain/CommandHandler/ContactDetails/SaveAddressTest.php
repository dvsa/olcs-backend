<?php

/**
 * Save Address Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ContactDetails;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\ContactDetails\SaveAddress;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as AddressEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Address as AddressRepo;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails as ContactDetailsRepo;
use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress as Cmd;

/**
 * Save Address Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SaveAddressTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new SaveAddress();
        $this->mockRepo('Address', AddressRepo::class);
        $this->mockRepo('ContactDetails', ContactDetailsRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            ContactDetailsEntity::CONTACT_TYPE_REGISTERED_ADDRESS
        ];

        $this->references = [
            Country::class => [
                'GB' => m::mock(Country::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommandUpdateWithoutChange()
    {
        /** @var AddressEntity $address */
        $address = m::mock(AddressEntity::class)->makePartial();
        $address->setVersion(1);
        $address->shouldReceive('updateAddress')
            ->with('address 1', null, null, null, 'Town', 'PostCode', $this->references[Country::class]['GB']);

        $data = [
            'id' => 111,
            'version' => 1,
            'addressLine1' => 'address 1',
            'town' => 'Town',
            'postcode' => 'PostCode',
            'countryCode' => 'GB'
        ];
        $command = Cmd::create($data);

        $this->repoMap['Address']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($address)
            ->shouldReceive('save')
            ->once()
            ->with($address);

        $result = $this->sut->handleCommand($command);

        $this->assertFalse($result->getFlag('hasChanged'));

        $expected = [
            'id' => [],
            'messages' => [
                'Address unchanged'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandUpdateWithChange()
    {
        /** @var AddressEntity $address */
        $address = m::mock(AddressEntity::class)->makePartial();
        $address->shouldReceive('updateAddress')
            ->with('address 1', null, null, null, 'Town', 'PostCode', $this->references[Country::class]['GB']);

        $data = [
            'id' => 111,
            'version' => 1,
            'addressLine1' => 'address 1',
            'town' => 'Town',
            'postcode' => 'PostCode',
            'countryCode' => 'GB'
        ];
        $command = Cmd::create($data);

        $this->repoMap['Address']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($address)
            ->shouldReceive('save')
            ->once()
            ->with($address)
            ->andReturnUsing(
                function (AddressEntity $address) {
                    $address->setVersion(2);
                }
            );

        $result = $this->sut->handleCommand($command);

        $this->assertTrue($result->getFlag('hasChanged'));

        $expected = [
            'id' => [],
            'messages' => [
                'Address updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandCreate()
    {
        $data = [
            'addressLine1' => 'address 1',
            'town' => 'Town',
            'postcode' => 'PostCode',
            'countryCode' => 'GB',
            'contactType' => ContactDetailsEntity::CONTACT_TYPE_REGISTERED_ADDRESS
        ];
        $command = Cmd::create($data);

        /** @var AddressEntity $savedAddress */
        $savedAddress = null;
        /** @var ContactDetailsEntity $savedContactDetails */
        $savedContactDetails = null;

        $this->repoMap['Address']->shouldReceive('save')
            ->once()
            ->andReturnUsing(
                function (AddressEntity $address) use (&$savedAddress) {
                    $address->setId(111);
                    $savedAddress = $address;
                }
            );

        $this->repoMap['ContactDetails']->shouldReceive('save')
            ->once()
            ->andReturnUsing(
                function (ContactDetailsEntity $contactDetails) use (&$savedContactDetails) {
                    $contactDetails->setId(222);
                    $savedContactDetails = $contactDetails;
                }
            );

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(AddressEntity::class, $savedAddress);
        $this->assertInstanceOf(ContactDetailsEntity::class, $savedContactDetails);

        $this->assertSame($savedAddress, $savedContactDetails->getAddress());
        $this->assertSame(
            $this->refData[ContactDetailsEntity::CONTACT_TYPE_REGISTERED_ADDRESS],
            $savedContactDetails->getContactType()
        );

        $expected = [
            'id' => [
                'address' => 111,
                'contactDetails' => 222
            ],
            'messages' => [
                'Address created',
                'Contact Details created'
            ]
        ];

        $this->assertTrue($result->getFlag('hasChanged'));

        $this->assertEquals($expected, $result->toArray());
    }
}
