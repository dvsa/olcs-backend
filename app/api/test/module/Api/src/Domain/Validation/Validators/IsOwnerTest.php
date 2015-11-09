<?php

/**
 * Is Owner Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Entity\User\User;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Validators\IsOwner;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Is Owner Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class IsOwnerTest extends MockeryTestCase
{
    /**
     * @var IsOwner
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new IsOwner();
    }

    public function testIsValidTrue()
    {
        $organisation = m::mock();

        $orgProvider = m::mock(OrganisationProviderInterface::class);
        $orgProvider->shouldReceive('getRelatedOrganisation')->andReturn($organisation);

        $user = m::mock(User::class);
        $user->shouldReceive('getRelatedOrganisation')->andReturn($organisation);

        $this->assertEquals(true, $this->sut->isValid($orgProvider, $user));
    }

    public function testIsValidFalse()
    {
        $organisation1 = m::mock();
        $organisation2 = m::mock();

        $orgProvider = m::mock(OrganisationProviderInterface::class);
        $orgProvider->shouldReceive('getRelatedOrganisation')->andReturn($organisation1);

        $user = m::mock(User::class);
        $user->shouldReceive('getRelatedOrganisation')->andReturn($organisation2);

        $this->assertEquals(false, $this->sut->isValid($orgProvider, $user));
    }
}
