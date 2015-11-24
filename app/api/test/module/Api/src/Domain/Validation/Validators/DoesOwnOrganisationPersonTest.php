<?php

/**
 * Does Own OrganisationPerson Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\DoesOwnOrganisationPerson;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson;
use Mockery as m;

/**
 * Does Own OrganisationPerson Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DoesOwnOrganisationPersonTest extends AbstractValidatorsTestCase
{
    /**
     * @var DoesOwnOrganisationPerson
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new DoesOwnOrganisationPerson();

        parent::setUp();
    }

    /**
     * @dataProvider provider
     */
    public function testIsValidTrue($isOwner, $expected)
    {
        $entity = m::mock(OrganisationPerson::class);

        $repo = $this->mockRepo('OrganisationPerson');
        $repo->shouldReceive('fetchById')->with(111)->andReturn($entity);

        $this->setIsValid('isOwner', [$entity], $isOwner);

        $this->assertEquals($expected, $this->sut->isValid(111));
    }

    public function provider()
    {
        return [
            [true, true],
            [false, false]
        ];
    }
}
