<?php

/**
 * Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\CompanySubsidiary;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\CompanySubsidiary\CompanySubsidiary;
use Zend\ServiceManager\ServiceManager;
use Dvsa\Olcs\Transfer\Query\CompanySubsidiary\CompanySubsidiary as Qry;

/**
 * Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CompanySubsidiaryTest extends AbstractHandlerTestCase
{
    /**
     * @var CompanySubsidiary
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CompanySubsidiary();

        parent::setUp();
    }

    /**
     * @dataProvider noContextProvider
     */
    public function testIsValidNoContext($isInternal, $expected)
    {
        $data = [
            'application' => null,
            'licence' => null
        ];

        $dto = Qry::create($data);

        $this->setIsGranted(Permission::INTERNAL_USER, $isInternal);

        $this->assertEquals($expected, $this->sut->isValid($dto));
    }

    public function testIsValidWithContextExternalNoOwnership()
    {
        $data = [
            'id' => 111,
            'application' => 222,
            'licence' => null
        ];

        $dto = Qry::create($data);

        $companySubsidiary = m::mock(\Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary::class);

        $mockCsRepo = $this->mockRepo('CompanySubsidiary');
        $mockCsRepo->shouldReceive('fetchUsingId')->with($dto)->andReturn($companySubsidiary);

        $user = $this->mockUser();

        $this->setIsGranted(Permission::INTERNAL_USER, false);
        $this->setIsValid('isOwner', [$companySubsidiary, $user], false);

        $this->assertEquals(false, $this->sut->isValid($dto));
    }

    /**
     * @dataProvider ownershipProvider
     */
    public function testIsValidWithContextAndOwnership($isInternal, $isOwner)
    {
        $data = [
            'id' => 111,
            'application' => null,
            'licence' => 222
        ];

        $dto = Qry::create($data);

        $licence = m::mock(Licence::class);

        $companySubsidiary = m::mock(\Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary::class);
        $companySubsidiary->shouldReceive('getLicence')->andReturn($licence);

        $mockCsRepo = $this->mockRepo('CompanySubsidiary');
        $mockCsRepo->shouldReceive('fetchUsingId')->with($dto)->andReturn($companySubsidiary);

        $mockLicenceRepo = $this->mockRepo('Licence');
        $mockLicenceRepo->shouldReceive('fetchById')->with(222)->andReturn($licence);

        $user = $this->mockUser();

        $this->setIsGranted(Permission::INTERNAL_USER, $isInternal);
        $this->setIsValid('isOwner', [$companySubsidiary, $user], $isOwner);

        $this->assertEquals(true, $this->sut->isValid($dto));
    }

    /**
     * @dataProvider licenceProviderProvider
     */
    public function testIsValidWithContextAndOwnershipWithMatching($application, $licence, $provider)
    {
        $data = [
            'id' => 111,
            'application' => $application,
            'licence' => $licence
        ];

        $dto = Qry::create($data);

        $licence = $this->$provider();

        $companySubsidiary = m::mock(\Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary::class);
        $companySubsidiary->shouldReceive('getLicence')->andReturn($licence);

        $mockCsRepo = $this->mockRepo('CompanySubsidiary');
        $mockCsRepo->shouldReceive('fetchUsingId')->with($dto)->andReturn($companySubsidiary);

        $this->setIsGranted(Permission::INTERNAL_USER, true);

        $this->assertEquals(true, $this->sut->isValid($dto));
    }

    /**
     * @dataProvider licenceProviderProvider
     */
    public function testIsValidWithContextAndOwnershipWithoutMatching($application, $licence, $provider)
    {
        $data = [
            'id' => 111,
            'application' => $application,
            'licence' => $licence
        ];

        $dto = Qry::create($data);

        $this->$provider();

        $licence = m::mock(Licence::class);

        $companySubsidiary = m::mock(\Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary::class);
        $companySubsidiary->shouldReceive('getLicence')->andReturn($licence);

        $mockCsRepo = $this->mockRepo('CompanySubsidiary');
        $mockCsRepo->shouldReceive('fetchUsingId')->with($dto)->andReturn($companySubsidiary);

        $this->setIsGranted(Permission::INTERNAL_USER, true);

        $this->assertEquals(false, $this->sut->isValid($dto));
    }

    public function licenceProviderProvider()
    {
        return [
            [222, null, 'getLicenceFromApplication'],
            [null, 222, 'getLicenceFromLicence']
        ];
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
