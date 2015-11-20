<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\CanAccessPreviousConviction;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Mockery as m;

/**
 * CanAccessPreviousConvictionTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CanAccessPreviousConvictionTest extends AbstractValidatorsTestCase
{
    /**
     * @var CanAccessPreviousConviction
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CanAccessPreviousConviction();

        parent::setUp();
    }

    public function testIsValidTransportManager()
    {
        $mockTm = m::mock();

        $entity = m::mock(Application::class);
        $entity->shouldReceive('getTransportManager')->with()->twice()->andReturn($mockTm);

        $user = $this->mockUser();
        $user->shouldReceive('getTransportManager')->with()->once()->andReturn($mockTm);

        $repo = $this->mockRepo('PreviousConviction');
        $repo->shouldReceive('fetchById')->with(111)->andReturn($entity);

        $this->assertEquals(true, $this->sut->isValid(111));
    }

    /**
     * @dataProvider provider
     */
    public function testIsValid($isOwner, $expected)
    {
        $entity = m::mock(Application::class);
        $entity->shouldReceive('getTransportManager')->with()->once()->andReturn(null);

        $this->setIsGranted(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null);

        $repo = $this->mockRepo('PreviousConviction');
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
