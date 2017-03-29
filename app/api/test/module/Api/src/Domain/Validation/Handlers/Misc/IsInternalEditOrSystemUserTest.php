<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\Misc;

use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalEditOrSystemUser;

/**
 * Is Internal Edit or System User Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class IsInternalEditOrSystemUserTest extends AbstractHandlerTestCase
{
    /**
     * @var IsInternalEditOrSystemUser
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new IsInternalEditOrSystemUser();

        parent::setUp();
    }

    public function testIsValidSysytem()
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);

        $this->setIsGranted(Permission::INTERNAL_EDIT, false);

        $mockUser = $this->mockUser();
        $mockUser->shouldReceive('isSystemUser')
            ->andReturn(true)
            ->once();

        $this->assertTrue($this->sut->isValid($dto));
    }

    public function testIsNotValidSysytem()
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);

        $this->setIsGranted(Permission::INTERNAL_EDIT, false);

        $mockUser = $this->mockUser();
        $mockUser->shouldReceive('isSystemUser')
            ->andReturn(false)
            ->once();

        $this->assertFalse($this->sut->isValid($dto));
    }
}
