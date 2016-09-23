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
        $mockUser->shouldReceive('isSystemUser')
            ->andReturn(true)
            ->once();

        $this->assertTrue($this->sut->isValid($dto));
    }

    public function testIsNonSystemUser()
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);

        $mockUser = $this->mockUser();
        $mockUser->shouldReceive('isSystemUser')
            ->andReturn(false)
            ->once();

        $this->assertFalse($this->sut->isValid($dto));
    }
}
