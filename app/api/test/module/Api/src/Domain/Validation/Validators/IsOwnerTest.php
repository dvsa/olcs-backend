<?php

/**
 * Is Owner Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Validators\IsOwner;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;

/**
 * Is Owner Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class IsOwnerTest extends AbstractValidatorsTestCase
{
    /**
     * @var IsOwner
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new IsOwner();

        parent::setUp();
    }

    public function testIsValidTrue()
    {
        $organisation = m::mock();

        /** @var OrganisationProviderInterface $orgProvider */
        $orgProvider = m::mock(OrganisationProviderInterface::class);
        $orgProvider->shouldReceive('getRelatedOrganisation')->andReturn($organisation);

        $user = $this->mockUser();
        $user->shouldReceive('getRelatedOrganisation')->andReturn($organisation);

        $this->assertEquals(true, $this->sut->isValid($orgProvider));
    }

    public function testIsValidFalse()
    {
        $organisation1 = m::mock();
        $organisation2 = m::mock();

        /** @var OrganisationProviderInterface $orgProvider */
        $orgProvider = m::mock(OrganisationProviderInterface::class);
        $orgProvider->shouldReceive('getRelatedOrganisation')->andReturn($organisation1);

        $user = $this->mockUser();
        $user->shouldReceive('getRelatedOrganisation')->andReturn($organisation2);

        $this->assertEquals(false, $this->sut->isValid($orgProvider));
    }

    public function testIsValidUserWithoutOrganisation()
    {
        /** @var OrganisationProviderInterface $orgProvider */
        $orgProvider = m::mock(OrganisationProviderInterface::class);

        $user = $this->mockUser();
        $user->shouldReceive('getRelatedOrganisation')->andReturn(null);

        $this->assertEquals(false, $this->sut->isValid($orgProvider));
    }

    public function testIsValidUserWithoutEntityOrganisation()
    {
        $organisation = m::mock();

        /** @var OrganisationProviderInterface $orgProvider */
        $orgProvider = m::mock(OrganisationProviderInterface::class);
        $orgProvider->shouldReceive('getRelatedOrganisation')->andReturn(null);

        $user = $this->mockUser();
        $user->shouldReceive('getRelatedOrganisation')->andReturn($organisation);

        $this->assertEquals(false, $this->sut->isValid($orgProvider));
    }
}
