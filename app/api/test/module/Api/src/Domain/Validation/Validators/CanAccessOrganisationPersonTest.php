<?php

/**
 * Can Access OrganisationPerson Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\CanAccessOrganisationPerson;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Mockery as m;

/**
 * Can Access OrganisationPerson Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CanAccessOrganisationPersonTest extends AbstractValidatorsTestCase
{
    /**
     * @var CanAccessOrganisationPerson
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new CanAccessOrganisationPerson();

        parent::setUp();
    }

    /**
     * @dataProvider provider
     */
    public function testIsValid($isOwner, $expected)
    {
        $this->setIsGranted(Permission::INTERNAL_USER, false);
        $this->auth->shouldReceive('getIdentity')->andReturn(null);
        $entity = m::mock(OrganisationPerson::class);

        $repo = $this->mockRepo('OrganisationPerson');
        $repo->shouldReceive('fetchById')->with(111)->andReturn($entity);

        $this->setIsValid('isOwner', [$entity], $isOwner);

        $this->assertEquals($expected, $this->sut->isValid(111));
    }

    /**
     * @dataProvider provider
     */
    public function testIsValidInternal($isOwner, $expected)
    {
        $this->setIsGranted(Permission::INTERNAL_USER, true);
        $entity = m::mock(OrganisationPerson::class);

        $repo = $this->mockRepo('OrganisationPerson');
        $repo->shouldReceive('fetchById')->with(111)->andReturn($entity);

        $this->setIsValid('isOwner', [$entity], $isOwner);

        $this->assertEquals(true, $this->sut->isValid(111));
    }

    public function provider()
    {
        return [
            [true, true],
            [false, false]
        ];
    }
}
