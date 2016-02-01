<?php

/**
 * Is Internal or System User Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\Misc;

use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalOrSystemUser;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider as PidIdentityProviderEntity;

/**
 * Is Internal or System User Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class IsInternalOrSystemUserTest extends AbstractHandlerTestCase
{
    /**
     * @var IsInternalOrSystemUser
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new IsInternalOrSystemUser();

        parent::setUp();
    }

    public function testIsValidSysytem()
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);

        $this->setIsGranted(Permission::INTERNAL_USER, false);
        $mockUser = $this->mockUser();
        $mockUser->shouldReceive('getTeam')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(PidIdentityProviderEntity::SYSTEM_TEAM)
                ->once()
                ->getMock()
            )
            ->twice()
            ->getMock();

        $this->assertTrue($this->sut->isValid($dto));
    }

    public function testIsNotValidSysytem()
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);

        $this->setIsGranted(Permission::INTERNAL_USER, false);
        $mockUser = $this->mockUser();
        $mockUser->shouldReceive('getTeam')
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->assertFalse($this->sut->isValid($dto));
    }
}
