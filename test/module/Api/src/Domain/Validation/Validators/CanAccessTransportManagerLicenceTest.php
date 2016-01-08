<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\CanAccessLicence;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Mockery as m;

/**
 * CanAccessTransportManagerLicenceTest
 *
 * @author Mat Evans <alex.peshkov@valtech.co.uk>
 */
class CanAccessTransportManagerLicenceTest extends AbstractValidatorsTestCase
{
    /**
     * @var CanAccessLicence
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new \Dvsa\Olcs\Api\Domain\Validation\Validators\CanAccessTransportManagerLicence();

        parent::setUp();
    }

    public function testIsValidSameTm()
    {
        $entity = m::mock(\Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence::class);
        $entity->shouldReceive('getTransportManager')->with()->once()->andReturn('TM');

        $repo = $this->mockRepo('TransportManagerLicence');
        $repo->shouldReceive('fetchById')->with(212)->andReturn($entity);

        $user = $this->mockUser();
        $user->shouldReceive('getTransportManager')->with()->once()->andReturn('TM');

        $this->assertEquals(true, $this->sut->isValid(212));
    }

    public function testIsValidNotSameTm()
    {
        $entity = m::mock(\Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence::class);
        $entity->shouldReceive('getTransportManager')->with()->once()->andReturn('TM');

        $repo = $this->mockRepo('TransportManagerLicence');
        $repo->shouldReceive('fetchById')->with(212)->andReturn($entity);

        $user = $this->mockUser();
        $user->shouldReceive('getTransportManager')->with()->once()->andReturn('TM DIFFERENT');

        $this->setIsGranted(Permission::INTERNAL_USER, true);

        $this->assertEquals(true, $this->sut->isValid(212));
    }
}
