<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\TransportManagerApplication;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\TransportManagerApplication\Delete;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider as PidIdentityProviderEntity;

/**
 * DeleteTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class DeleteTest extends AbstractHandlerTestCase
{
    /**
     * @var Delete
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new Delete();

        parent::setUp();
    }

    /**
     * @dataProvider provider
     */
    public function testIsValid($canAccess, $expected, $userId)
    {
        $mockUser = $this->mockUser();
        $mockUser->shouldReceive('getId')
            ->andReturn($userId)
            ->once()
            ->getMock();

        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);
        $dto->shouldReceive('getIds')->andReturn([19, 11, 2015]);

        $mockValidator = m::mock();
        $this->validatorManager->setService('canAccessTransportManagerApplication', $mockValidator);

        $mockValidator->shouldReceive('isValid')->with(19)->andReturn($canAccess);
        $mockValidator->shouldReceive('isValid')->with(11)->andReturn($canAccess);
        $mockValidator->shouldReceive('isValid')->with(2015)->andReturn($canAccess);

        $this->assertSame($expected, $this->sut->isValid($dto));
    }

    public function provider()
    {
        return [
            [true, true, 10],
            [false, false, 10],
            [false, true, PidIdentityProviderEntity::SYSTEM_USER]
        ];
    }
}
