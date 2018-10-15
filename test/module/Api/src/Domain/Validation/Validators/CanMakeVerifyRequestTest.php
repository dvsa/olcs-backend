<?php


namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanMakeVerifyRequest as Sut;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;

class CanMakeVerifyRequestTest extends AbstractHandlerTestCase
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
     * testIsValidForOperatorUser
     */
    public function testIsValidForOperatorUser()
    {

        /** @var CommandInterface $dto */

        $this->setIsGranted(Permission::OPERATOR_ADMIN, false);
        $this->setIsGranted(Permission::OPERATOR_USER, true);
        $this->assertTrue($this->sut->isValid($dto));
    }

    public function testIsValidForOperator()
    {

        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);
        $this->setIsGranted(Permission::OPERATOR_ADMIN, true);
        $this->assertTrue($this->sut->isValid($dto));
    }

    /**
     * testIsValidForTransportManagerContextOnly
     */
    public function testIsValidForTransportManagerOnly()
    {

        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);
        $this->setIsGranted(Permission::OPERATOR_ADMIN, false);
        $this->setIsGranted(Permission::OPERATOR_USER, false);
        $this->setIsGranted(Permission::TRANSPORT_MANAGER, true);


        $this->assertTrue($this->sut->isValid($dto));
    }

    public function testFailsValidationIfNotProcessSignatureAndTMA()
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(GetAuthRequest::class);

        $this->setIsGranted(Permission::OPERATOR_ADMIN, false);
        $this->setIsGranted(Permission::OPERATOR_USER, false);
        $this->setIsGranted(Permission::TRANSPORT_MANAGER, true);

        $this->assertFalse($this->sut->isValid($dto));
    }

}