<?php

/**
 * Update Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\CompanySubsidiary\Licence;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\CompanySubsidiary\Licence\Update;
use Zend\ServiceManager\ServiceManager;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateCompanySubsidiary as Cmd;

/**
 * Update Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateTest extends AbstractHandlerTestCase
{
    /**
     * @var Update
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Update();

        parent::setUp();
    }

    public function testIsValidNoContext()
    {
        $data = [
            'licence' => null
        ];

        $dto = Cmd::create($data);

        $this->assertFalse($this->sut->isValid($dto));
    }

    public function testIsValidWithContextNoAccess()
    {
        $data = [
            'id' => 111,
            'licence' => 222
        ];

        $licence = $this->getLicenceFromLicence();
        $licence->shouldReceive('getId')->andReturn(222);

        $dto = Cmd::create($data);

        $this->setIsValid('canAccessLicence', [222], false);

        $this->assertEquals(false, $this->sut->isValid($dto));
    }

    public function testIsValidWithContextNoOwnership()
    {
        $data = [
            'id' => 111,
            'licence' => 222
        ];

        $licence = $this->getLicenceFromLicence();
        $licence->shouldReceive('getId')->andReturn(222);

        $dto = Cmd::create($data);

        $this->setIsValid('canAccessCompanySubsidiary', [111], false);
        $this->setIsValid('canAccessLicence', [222], true);

        $this->assertEquals(false, $this->sut->isValid($dto));
    }

    public function testIsValidWithContextAndOwnership()
    {
        $data = [
            'id' => 111,
            'licence' => 222
        ];

        $dto = Cmd::create($data);

        $licence = $this->getLicenceFromLicence();
        $licence->shouldReceive('getId')->andReturn(222);

        $companySubsidiary = m::mock(\Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary::class);
        $companySubsidiary->shouldReceive('getLicence')->andReturn($licence);

        $mockCsRepo = $this->mockRepo('CompanySubsidiary');
        $mockCsRepo->shouldReceive('fetchByIds')->with([111])->andReturn([$companySubsidiary]);

        $this->setIsValid('canAccessCompanySubsidiary', [111], true);
        $this->setIsValid('canAccessLicence', [222], true);

        $this->assertEquals(true, $this->sut->isValid($dto));
    }

    public function testIsValidWithContextAndOwnershipWithoutMatching()
    {
        $data = [
            'id' => 111,
            'licence' => 222
        ];

        $dto = Cmd::create($data);

        $lic = $this->getLicenceFromLicence();
        $lic->shouldReceive('getId')->andReturn(222);

        $licence = m::mock(Licence::class);

        $companySubsidiary = m::mock(\Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary::class);
        $companySubsidiary->shouldReceive('getLicence')->andReturn($licence);

        $mockCsRepo = $this->mockRepo('CompanySubsidiary');
        $mockCsRepo->shouldReceive('fetchByIds')->with([111])->andReturn([$companySubsidiary]);

        $this->setIsValid('canAccessCompanySubsidiary', [111], true);
        $this->setIsValid('canAccessLicence', [222], true);

        $this->assertEquals(false, $this->sut->isValid($dto));
    }

    public function getLicenceFromLicence()
    {
        $licence = m::mock(Licence::class);

        $mockLicenceRepo = $this->mockRepo('Licence');
        $mockLicenceRepo->shouldReceive('fetchById')->with(222)->andReturn($licence);

        return $licence;
    }
}
