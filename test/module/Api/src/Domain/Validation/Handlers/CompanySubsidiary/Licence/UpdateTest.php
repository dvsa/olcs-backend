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

    /**
     * @dataProvider noContextProvider
     */
    public function testIsValidNoContext($isInternal, $expected)
    {
        $data = [
            'licence' => null
        ];

        $dto = Cmd::create($data);

        $this->setIsGranted(Permission::INTERNAL_USER, $isInternal);

        $this->assertEquals($expected, $this->sut->isValid($dto));
    }

    public function testIsValidWithContextExternalNoOwnership()
    {
        $data = [
            'id' => 111,
            'licence' => 222
        ];

        $dto = Cmd::create($data);

        $this->setIsGranted(Permission::INTERNAL_USER, false);
        $this->setIsValid('doesOwnCompanySubsidiary', [111], false);

        $this->assertEquals(false, $this->sut->isValid($dto));
    }

    /**
     * @dataProvider ownershipProvider
     */
    public function testIsValidWithContextAndOwnership($isInternal, $isOwner)
    {
        $data = [
            'id' => 111,
            'licence' => 222
        ];

        $dto = Cmd::create($data);

        $licence = $this->getLicenceFromLicence();

        $companySubsidiary = m::mock(\Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary::class);
        $companySubsidiary->shouldReceive('getLicence')->andReturn($licence);

        $mockCsRepo = $this->mockRepo('CompanySubsidiary');
        $mockCsRepo->shouldReceive('fetchByIds')->with([111])->andReturn([$companySubsidiary]);

        $this->setIsGranted(Permission::INTERNAL_USER, $isInternal);
        $this->setIsValid('doesOwnCompanySubsidiary', [111], $isOwner);

        $this->assertEquals(true, $this->sut->isValid($dto));
    }

    public function testIsValidWithContextAndOwnershipWithMatching()
    {
        $data = [
            'id' => 111,
            'licence' => 222
        ];

        $dto = Cmd::create($data);

        $licence = $this->getLicenceFromLicence();

        $companySubsidiary = m::mock(\Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary::class);
        $companySubsidiary->shouldReceive('getLicence')->andReturn($licence);

        $mockCsRepo = $this->mockRepo('CompanySubsidiary');
        $mockCsRepo->shouldReceive('fetchByIds')->with([111])->andReturn([$companySubsidiary]);

        $this->setIsGranted(Permission::INTERNAL_USER, true);

        $this->assertEquals(true, $this->sut->isValid($dto));
    }

    public function testIsValidWithContextAndOwnershipWithoutMatching()
    {
        $data = [
            'id' => 111,
            'licence' => 222
        ];

        $dto = Cmd::create($data);

        $this->getLicenceFromLicence();

        $licence = m::mock(Licence::class);

        $companySubsidiary = m::mock(\Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary::class);
        $companySubsidiary->shouldReceive('getLicence')->andReturn($licence);

        $mockCsRepo = $this->mockRepo('CompanySubsidiary');
        $mockCsRepo->shouldReceive('fetchByIds')->with([111])->andReturn([$companySubsidiary]);

        $this->setIsGranted(Permission::INTERNAL_USER, true);

        $this->assertEquals(false, $this->sut->isValid($dto));
    }

    public function getLicenceFromLicence()
    {
        $licence = m::mock(Licence::class);

        $mockLicenceRepo = $this->mockRepo('Licence');
        $mockLicenceRepo->shouldReceive('fetchById')->with(222)->andReturn($licence);

        return $licence;
    }

    public function noContextProvider()
    {
        return [
            // [isInternal, expected]
            [true, true],
            [false, false]
        ];
    }

    public function ownershipProvider()
    {
        return [
            // [isInternal, isOwner]
            [true, false],
            [false, true]
        ];
    }
}
