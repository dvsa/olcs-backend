<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanVerify as Sut;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
/**
 * Can Manage User Test
 */
class CanVerifyTest extends AbstractHandlerTestCase
{
    /**
     * @var Sut
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Sut();

        parent::setUp();
    }

    /**
     * @dataProvider provider
     *
     * @param $permission
     * @param $expected
     */
    public function testIsValidForOperatorUser()
    {

        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);
        $this->setIsGranted(Permission::OPERATOR_ADMIN, false);
        $this->setIsGranted(Permission::OPERATOR_USER, true);

        $dto->shouldReceive('getTransportManagerApplication')->andReturn(0);
        $this->assertTrue($this->sut->isValid($dto));
    }

    public function testIsValidForOperator()
    {

        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);
        $this->setIsGranted(Permission::OPERATOR_ADMIN, true);
       

        $dto->shouldReceive('getTransportManagerApplication')->andReturn(0);
        $this->assertTrue($this->sut->isValid($dto));
    }

    /**
     * testIsValidForTransportManagerContextOnly
     */
    public function testIsValidForTransportManagerContextOnly()
    {

        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);
        $this->setIsGranted(Permission::OPERATOR_ADMIN, false);
        $this->setIsGranted(Permission::OPERATOR_USER, false);
        $this->setIsGranted(Permission::TRANSPORT_MANAGER, true);
        $dto->shouldReceive('getTransportManagerApplication')->andReturn(1);

        $this->assertTrue($this->sut->isValid($dto));
    }

    public function provider()
    {
        return [
            [Permission::OPERATOR_ADMIN, true],
            [Permission::OPERATOR_USER, true],
            [Permission::LOCAL_AUTHORITY_ADMIN, false],
        ];
    }
}

