<?php

/**
 * Create Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\CompanySubsidiary\Licence;

use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\CompanySubsidiary\Licence\Create;
use Zend\ServiceManager\ServiceManager;
use Dvsa\Olcs\Transfer\Command\Licence\CreateCompanySubsidiary as Cmd;

/**
 * Create Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateTest extends AbstractHandlerTestCase
{
    /**
     * @var Create
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Create();

        parent::setUp();
    }

    public function testIsValidInternal()
    {
        $dto = Cmd::create([]);

        $this->setIsGranted(Permission::INTERNAL_USER, true);

        $this->assertTrue($this->sut->isValid($dto));
    }

    public function testIsValidOwner()
    {
        $dto = Cmd::create(['licence' => 111]);

        $this->setIsGranted(Permission::INTERNAL_USER, false);
        $this->setIsValid('doesOwnLicence', [111], true);

        $this->assertTrue($this->sut->isValid($dto));
    }

    public function testIsValidNotOwner()
    {
        $dto = Cmd::create(['licence' => 111]);

        $this->setIsGranted(Permission::INTERNAL_USER, false);
        $this->setIsValid('doesOwnLicence', [111], false);

        $this->assertFalse($this->sut->isValid($dto));
    }
}
