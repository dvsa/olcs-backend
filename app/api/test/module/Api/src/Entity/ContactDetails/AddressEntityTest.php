<?php

namespace Dvsa\OlcsTest\Api\Entity\ContactDetails;

use Mockery as m;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as Entity;

/**
 * Address Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class AddressEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testUpdateAddress()
    {
        $address = new Address();
        $country = m::mock(Country::class);

        $address->updateAddress(
            'address 1',
            'address 2',
            'address 3',
            'address 4',
            'town',
            'postcode',
            $country
        );

        $this->assertEquals('address 1', $address->getAddressLine1());
        $this->assertEquals('address 2', $address->getAddressLine2());
        $this->assertEquals('address 3', $address->getAddressLine3());
        $this->assertEquals('address 4', $address->getAddressLine4());
        $this->assertEquals('town', $address->getTown());
        $this->assertEquals('postcode', $address->getPostcode());
        $this->assertSame($country, $address->getCountryCode());
    }

    public function testIsEmpty()
    {
        $address = new Address();

        $this->assertEquals(true, $address->isEmpty());

        $address->updateAddress('address 1');
        $this->assertEquals(false, $address->isEmpty());
    }
}
