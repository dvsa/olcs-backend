<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanMakeVerifyRequest as Sut;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;

class CanMakeVerifyRequestTest extends AbstractHandlerTestCase
{
    /**
     * @var Sut
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new Sut();

        parent::setUp();
    }

    public function testIsValidForOperatorUser(): void
    {
        $dto = m::mock(CommandInterface::class);
        $this->setIsGranted(Permission::OPERATOR_ADMIN, null);
        $this->setIsGranted(Permission::OPERATOR_TC, null);
        $this->setIsGranted(Permission::OPERATOR_USER, true);
        $this->assertTrue($this->sut->isValid($dto));
    }

    public function testIsValidForOperatorAdmin(): void
    {
        $dto = m::mock(CommandInterface::class);
        $this->setIsGranted(Permission::OPERATOR_ADMIN, true);

        $this->assertTrue($this->sut->isValid($dto));
    }

    public function testIsValidForOperatorTransportConsultant(): void
    {
        $dto = m::mock(CommandInterface::class);
        $this->setIsGranted(Permission::OPERATOR_ADMIN, false);
        $this->setIsGranted(Permission::OPERATOR_TC, true);

        $this->assertTrue($this->sut->isValid($dto));
    }

    public function testIsValidForTransportManagerOnly(): void
    {
        $dto = m::mock(CommandInterface::class);
        $this->setIsGranted(Permission::OPERATOR_ADMIN, false);
        $this->setIsGranted(Permission::OPERATOR_TC, false);
        $this->setIsGranted(Permission::OPERATOR_USER, false);
        $this->setIsGranted(Permission::TRANSPORT_MANAGER, true);

        $this->assertTrue($this->sut->isValid($dto));
    }
}
