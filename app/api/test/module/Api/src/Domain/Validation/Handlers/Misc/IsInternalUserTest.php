<?php

/**
 * Is Internal User Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\CompanySubsidiary\Application;

use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Zend\ServiceManager\ServiceManager;

/**
 * Is Internal User Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class IsInternalUserTest extends AbstractHandlerTestCase
{
    /**
     * @var IsInternalUser
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new IsInternalUser();

        parent::setUp();
    }

    public function testIsValidInternal()
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);

        $this->setIsGranted(Permission::INTERNAL_USER, true);

        $this->assertTrue($this->sut->isValid($dto));
    }

    public function testIsValidInternalFail()
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);

        $this->setIsGranted(Permission::INTERNAL_USER, false);

        $this->assertFalse($this->sut->isValid($dto));
    }
}
