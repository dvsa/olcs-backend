<?php

/**
 * Update Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\CompanySubsidiary\Application;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\CompanySubsidiary\Application\Update;
use Zend\ServiceManager\ServiceManager;
use Dvsa\Olcs\Transfer\Command\Application\UpdateCompanySubsidiary as Cmd;

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
            'application' => null
        ];

        $dto = Cmd::create($data);

        $this->setIsGranted(Permission::INTERNAL_USER, $isInternal);

        $this->assertEquals($expected, $this->sut->isValid($dto));
    }

    public function testIsValidWithContextExternalNoOwnership()
    {
        $data = [
            'id' => 111,
            'application' => 222
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
            'application' => 222
        ];

        $dto = Cmd::create($data);

        $licence = $this->getLicenceFromApplication();

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
            'application' => 222
        ];

        $dto = Cmd::create($data);

        $licence = $this->getLicenceFromApplication();

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
            'application' => 222
        ];

        $dto = Cmd::create($data);

        $this->getLicenceFromApplication();

        $licence = m::mock(Licence::class);

        $companySubsidiary = m::mock(\Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary::class);
        $companySubsidiary->shouldReceive('getLicence')->andReturn($licence);

        $mockCsRepo = $this->mockRepo('CompanySubsidiary');
        $mockCsRepo->shouldReceive('fetchByIds')->with([111])->andReturn([$companySubsidiary]);

        $this->setIsGranted(Permission::INTERNAL_USER, true);

        $this->assertEquals(false, $this->sut->isValid($dto));
    }

    public function getLicenceFromApplication()
    {
        $licence = m::mock(Licence::class);

        $application = m::mock(Application::class);
        $application->shouldReceive('getLicence')->andReturn($licence);

        $mockApplicationRepo = $this->mockRepo('Application');
        $mockApplicationRepo->shouldReceive('fetchById')->with(222)->andReturn($application);

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
