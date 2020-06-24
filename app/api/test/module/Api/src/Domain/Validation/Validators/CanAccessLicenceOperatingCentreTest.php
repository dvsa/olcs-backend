<?php

/**
 * Can Access Licence Operating Centre Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\CanAccessLicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Mockery as m;

/**
 * Can Access Licence Operating Centre Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CanAccessLicenceOperatingCentreTest extends AbstractValidatorsTestCase
{
    /**
     * @var CanAccessLicenceOperatingCentre
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new CanAccessLicenceOperatingCentre();

        parent::setUp();
    }

    /**
     * @dataProvider provider
     */
    public function testIsValid($isOwner, $expected)
    {
        $this->setIsGranted(Permission::INTERNAL_USER, false);
        $this->auth->shouldReceive('getIdentity')->andReturn(null);
        $entity = m::mock(LicenceOperatingCentre::class);

        $repo = $this->mockRepo('LicenceOperatingCentre');
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
        $entity = m::mock(LicenceOperatingCentre::class);

        $repo = $this->mockRepo('LicenceOperatingCentre');
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
