<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\PrivateHireLicence;

use Dvsa\Olcs\Api\Domain\Validation\Validators\CanAccessLicence;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\PrivateHireLicence\PrivateHireLicence;

class PrivateHireLicenceTest extends AbstractHandlerTestCase
{
    /**
     * @var PrivateHireLicence
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new PrivateHireLicence();

        parent::setUp();
    }

    public function testIsValidInternalUser()
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);

        $this->auth->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)->once()
            ->andReturn(true);

        $this->assertSame(true, $this->sut->isValid($dto));
    }

    /**
     * @dataProvider provider
     */
    public function testIsValid($canAccess, $expected)
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);
        $dto->shouldReceive('getLicence')->andReturn(1);

        $this->auth->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)->once()
            ->andReturn(false);

        $mockValidator = m::mock(CanAccessLicence::class);
        $this->validatorManager->setService('canAccessLicence', $mockValidator);

        $mockValidator->shouldReceive('isValid')->with(1)->andReturn($canAccess);

        $this->assertSame($expected, $this->sut->isValid($dto));
    }

    /**
     * @return array
     */
    public function provider()
    {
        return [
            [true, true],
            [false, false],
        ];
    }
}
