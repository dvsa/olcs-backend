<?php

namespace Dvsa\OlcsTest\Api\Mvc;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Listener\OlcsEntityListener;
use Dvsa\OlcsTest\Api\Listener\Stub\EntityStub;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

/**
 * @covers \Dvsa\Olcs\Api\Listener\OlcsEntityListener
 */
class OlcsEntityListenerTest extends MockeryTestCase
{
    /** @var  OlcsEntityListener */
    private $sut;

    /** @var  m\MockInterface|ServiceLocatorInterface */
    private $mockSl;
    /** @var  m\MockInterface */
    private $mockAuth;

    /** @var  m\MockInterface */
    private $mockEm;
    /** @var  m\MockInterface */
    private $mockUow;
    /** @var  m\MockInterface */
    private $mockMeta;

    public function setUp()
    {
        $this->mockMeta = m::mock(\Doctrine\ORM\Mapping\ClassMetadata::class);
        $this->mockUow = m::mock(\Doctrine\ORM\UnitOfWork::class);

        $this->mockEm = m::mock(\Doctrine\ORM\EntityManager::class);
        $this->mockEm->shouldReceive('getUnitOfWork')->atMost(1)->andReturn($this->mockUow);

        $this->mockAuth = m::mock(AuthorizationService::class);

        $this->mockSl = m::mock(ServiceLocatorInterface::class);
        $this->mockSl
            ->shouldReceive('get')
            ->andReturnUsing(
                function ($key) {
                    $map = [
                        AuthorizationService::class => $this->mockAuth,
                    ];

                    return $map[$key];
                }
            );

        $this->sut = (new OlcsEntityListener())->createService($this->mockSl);
    }

    public function testGetSubscribedEvents()
    {
        static::assertEquals(['preSoftDelete'], $this->sut->getSubscribedEvents());
    }

    public function testUpdateFieldMethodNotExists()
    {
        $mockEntity = m::mock();

        $this->mockAuth->shouldReceive('getIdentity->getUser')->andReturn(m::mock());

        $this->mockMeta
            ->shouldReceive('getReflectionProperty')->never();

        $this->mockEm
            ->shouldReceive('getClassMetadata')->once()->andReturn($this->mockMeta)
            ->shouldReceive('persist')->never();

        $this->mockUow
            ->shouldReceive('scheduleExtraUpdate')->never();

        //  call
        $lifecycleEvent = new LifecycleEventArgs($mockEntity, $this->mockEm);
        $this->sut->preSoftDelete($lifecycleEvent);
    }

    public function testUpdateFieldNotNotify()
    {
        $mockEntity = m::mock(Entity\Note\Note::class);

        $this->mockAuth->shouldReceive('getIdentity->getUser')->andReturn(m::mock());

        $mockProperty = m::mock()
            ->shouldReceive('getValue')->once()->with($mockEntity)->andReturn('unit_OldVal')
            ->shouldReceive('setValue')->once()->with($mockEntity, null)
            ->getMock();

        $this->mockMeta
            ->shouldReceive('getReflectionProperty')->once()->andReturn($mockProperty)
            ->shouldReceive('hasAssociation')->once()->andReturn(false);

        $this->mockEm
            ->shouldReceive('getClassMetadata')->once()->andReturn($this->mockMeta)
            ->shouldReceive('persist')->never();

        $this->mockUow
            ->shouldReceive('propertyChanged')->never()
            ->shouldReceive('scheduleExtraUpdate')->once();

        //  call
        $lifecycleEvent = new LifecycleEventArgs($mockEntity, $this->mockEm);
        $this->sut->preSoftDelete($lifecycleEvent);
    }

    /**
     * @dataProvider dpTestModifiedBy
     */
    public function testModifiedBy($currentUser, $expect)
    {
        $mockEntity = new EntityStub();

        $this->mockAuth->shouldReceive('getIdentity->getUser')->andReturn($currentUser);

        $field = 'lastModifiedBy';
        $oldValue = 'unit_OldVal';

        $mockPropery = m::mock()
            ->shouldReceive('getValue')->once()->with($mockEntity)->andReturn($oldValue)
            ->shouldReceive('setValue')->once()->with($mockEntity, $expect)
            ->getMock();

        $this->mockMeta
            ->shouldReceive('getReflectionProperty')->once()->with($field)->andReturn($mockPropery)
            ->shouldReceive('hasAssociation')->once()->with($field)->andReturn(true);

        $this->mockEm
            ->shouldReceive('getClassMetadata')->once()->with(EntityStub::class)->andReturn($this->mockMeta)
            ->shouldReceive('persist')->times($expect ? 1 : 0)->with($expect);

        $this->mockUow
            ->shouldReceive('propertyChanged')->once()->with($mockEntity, $field, $oldValue, $expect)
            ->shouldReceive('scheduleExtraUpdate')->once()->with(
                $mockEntity,
                [
                    $field => [$oldValue, $expect],
                ]
            );

        //  call
        $lifecycleEvent = new LifecycleEventArgs($mockEntity, $this->mockEm);
        $this->sut->preSoftDelete($lifecycleEvent);
    }

    public function dpTestModifiedBy()
    {
        /** @var Entity\User\User $mockUser */
        $mockUser = m::mock(Entity\User\User::class)->makePartial();
        $mockUser->setId(1);
        $mockUser->setPid('abc');

        return [
            [
                'currentUser' => $mockUser,
                'expectUpdate' => $mockUser,
            ],
            [
                'currentUser' => Entity\User\User::anon(),
                'expect' => null,
            ],
            [
                'currentUser' => null,
                'expect' => null,
            ],
        ];
    }
}
