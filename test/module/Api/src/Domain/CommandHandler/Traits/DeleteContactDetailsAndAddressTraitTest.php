<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Traits;

use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as AddressEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Address as AddressRepo;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails as ContactDetailsRepo;
use Mockery as m;

/**
 * Although this trait contains only private methods
 * we are testing them
 * to ensure changes in the trait are highlighted early
 * as potential impact could be high
 */
class DeleteContactDetailsAndAddressTraitTest extends CommandHandlerTestCase
{

    public function setUp()
    {
        $this->sut = new DeleteContactDetailsAndAddressTraitStub();
        $this->mockRepo('Address', AddressRepo::class);
        $this->mockRepo('ContactDetails', ContactDetailsRepo::class);

        parent::setUp();
    }

    public function testInjectReposWhenNotSet()
    {
        $this->sut = new DeleteContactDetailsAndAddressTraitStub();
        $this->invokeMethod($this->sut, 'injectRepos');
        $this->assertEquals(['ContactDetails', 'Address'], $this->sut->getExtraRepos());
    }

    public function testInjectRepos()
    {
        $this->sut = new DeleteContactDetailsAndAddressTraitStub();
        $this->sut->setExtraRepos(['ContactDetails', 'Address']);
        $this->invokeMethod($this->sut, 'injectRepos');
        $this->assertEquals(['ContactDetails', 'Address'], $this->sut->getExtraRepos());
    }

    public function testMaybeDeleteContactDetailsAndAddressWithAddressNull()
    {
        $contactDetails = m::mock(ContactDetailsEntity::class);
        $contactDetails->shouldReceive('getAddress')->andReturn(null);
        $this->repoMap['ContactDetails']->shouldReceive('delete')->with($contactDetails);

        $this->invokeMethod($this->sut, 'maybeDeleteContactDetailsAndAddress', [$contactDetails]);
    }

    public function testMaybeDeleteContactDetailsAndAddressWithCdNull()
    {
        $contactDetails = null;
        $this->invokeMethod($this->sut, 'maybeDeleteContactDetailsAndAddress', [$contactDetails]);
    }

    public function testMaybeDeleteContactDetailsAndAddress()
    {
        $address = m::mock(AddressEntity::class);
        $this->repoMap['Address']->shouldReceive('delete')->with($address);
        $contactDetails = m::mock(ContactDetailsEntity::class);
        $contactDetails->shouldReceive('getAddress')->andReturn($address);
        $this->repoMap['ContactDetails']->shouldReceive('delete')->with($contactDetails);

        $this->invokeMethod($this->sut, 'maybeDeleteContactDetailsAndAddress', [$contactDetails]);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    private function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
