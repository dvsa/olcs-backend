<?php

/**
 * Is System User Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\Misc;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSystemUser;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider as PidIdentityProviderEntity;

/**
 * Is System User Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class IsSystemUserTest extends AbstractHandlerTestCase
{
    /**
     * @var IsSystemUser
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new IsSystemUser();
        parent::setUp();
    }

    public function testIsSystemUser()
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);

        $mockUser = $this->mockUser();
        $mockUser->shouldReceive('getId')
            ->andReturn(PidIdentityProviderEntity::SYSTEM_USER)
            ->once()
            ->getMock();

        $this->assertTrue($this->sut->isValid($dto));
    }
}
