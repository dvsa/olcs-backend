<?php

/**
 * Can Access TxcInbox Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\CanAccessTxcInbox;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority;
use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\User\User;
use Mockery as m;

/**
 * Can Access TxcInbox Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class CanAccessTxcInboxTest extends AbstractValidatorsTestCase
{
    /**
     * @var CanAccessTxcInbox
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CanAccessTxcInbox();

        parent::setUp();
    }

    /**
     * @dataProvider provider
     */
    public function testIsValid($canAccess, $expected)
    {
        $this->setIsGranted(Permission::INTERNAL_USER, false);

        $this->auth->shouldReceive('isGranted')->with(Permission::LOCAL_AUTHORITY_USER, null)
            ->andReturn($canAccess);

        $this->auth->shouldReceive('isGranted')->with(Permission::LOCAL_AUTHORITY_ADMIN, null)
            ->andReturn($canAccess);

        $mockLocalAuthority = m::mock(LocalAuthority::class)->makePartial();
        $mockLocalAuthority->setId(999);

        $mockUser = m::mock(User::class)->makePartial();

        $mockIdentity = m::mock();
        $mockIdentity->shouldReceive('getUser')->andReturn($mockUser);
        $this->auth->shouldReceive('getIdentity')->andReturn($mockIdentity);
        $entity = m::mock(TxcInbox::class)->makePartial();
        if ($canAccess) {
            $mockUser->setLocalAuthority($mockLocalAuthority);
            $entity->shouldReceive('getLocalAuthority')->once()->andReturn($mockLocalAuthority);
        }

        $repo = $this->mockRepo('TxcInbox');
        $repo->shouldReceive('fetchById')->with(111)->andReturn($entity);

        $this->assertEquals($expected, $this->sut->isValid(111));
    }

    public function testIsValidWithEmptyId()
    {
        $this->assertFalse($this->sut->isValid(null));
    }

    /**
     * @dataProvider provider
     */
    public function testIsValidInternal($canAccess, $expected)
    {
        $this->setIsGranted(Permission::INTERNAL_USER, $canAccess);

        if (!$canAccess) {
            $this->auth->shouldReceive('isGranted')->with(Permission::LOCAL_AUTHORITY_USER, null)
                ->andReturn($canAccess);
            $this->auth->shouldReceive('isGranted')->with(Permission::LOCAL_AUTHORITY_ADMIN, null)
                ->andReturn($canAccess);
        }
        $entity = m::mock(TxcInbox::class)->makePartial();

        $repo = $this->mockRepo('TxcInbox');
        $repo->shouldReceive('fetchById')->with(111)->andReturn($entity);

        $this->assertEquals($expected, $this->sut->isValid(111));
    }

    public function testIsValidWithOtherLocalAuthority()
    {
        $this->setIsGranted(Permission::INTERNAL_USER, false);

        $this->auth->shouldReceive('isGranted')->with(Permission::LOCAL_AUTHORITY_USER, null)
            ->andReturn(true);

        $mockLocalAuthority = m::mock(LocalAuthority::class)->makePartial();
        $mockLocalAuthority->setId(999);

        // mock other LA to test that 1 LA cannot update another LA's entities
        $mockOtherLocalAuthority = m::mock(LocalAuthority::class)->makePartial();
        $mockOtherLocalAuthority->setId(111);

        $mockUser = m::mock(User::class)->makePartial();

        $mockIdentity = m::mock();
        $mockIdentity->shouldReceive('getUser')->andReturn($mockUser);
        $this->auth->shouldReceive('getIdentity')->andReturn($mockIdentity);
        $entity = m::mock(TxcInbox::class)->makePartial();

        $mockUser->setLocalAuthority($mockLocalAuthority);
        $entity->shouldReceive('getLocalAuthority')->once()->andReturn($mockOtherLocalAuthority);

        $repo = $this->mockRepo('TxcInbox');
        $repo->shouldReceive('fetchById')->with(111)->andReturn($entity);

        $this->assertFalse($this->sut->isValid(111));
    }

    public function testIsValidWithOtherUsers()
    {
        $this->auth->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)
            ->andReturn(false);
        $this->auth->shouldReceive('isGranted')->with(Permission::LOCAL_AUTHORITY_USER, null)
            ->andReturn(false);
        $this->auth->shouldReceive('isGranted')->with(Permission::LOCAL_AUTHORITY_ADMIN, null)
            ->andReturn(false);

        $this->assertFalse($this->sut->isValid(5));
    }

    public function provider()
    {
        return [
            [true, true],
            [false, false]
        ];
    }
}
