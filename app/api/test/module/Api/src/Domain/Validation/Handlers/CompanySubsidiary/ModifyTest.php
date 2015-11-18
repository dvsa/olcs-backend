<?php

/**
 * Modify Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\CompanySubsidiary;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\CompanySubsidiary\Modify;
use Zend\ServiceManager\ServiceManager;
use Dvsa\Olcs\Transfer\Query\CompanySubsidiary\CompanySubsidiary as Qry;

/**
 * Modify Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ModifyTest extends AbstractHandlerTestCase
{
    /**
     * @var Modify
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Modify();

        parent::setUp();
    }

    public function testIsValidNoContext()
    {
        $data = [
            'application' => null,
            'licence' => null
        ];

        $dto = Qry::create($data);

        $this->assertFalse($this->sut->isValid($dto));
    }

    /**
     * @dataProvider licenceProviderProvider
     */
    public function testIsValidWithContextNoAccess($applicationId, $licenceId, $provider)
    {
        $data = [
            'id' => 111,
            'application' => $applicationId,
            'licence' => $licenceId
        ];

        $licence = $this->$provider();
        $licence->shouldReceive('getId')->andReturn(123);

        $dto = Qry::create($data);

        $this->setIsValid('canAccessLicence', [123], false);

        $this->assertEquals(false, $this->sut->isValid($dto));
    }

    /**
     * @dataProvider licenceProviderProvider
     */
    public function testIsValidWithContextNoOwnership($applicationId, $licenceId, $provider)
    {
        $data = [
            'id' => 111,
            'application' => $applicationId,
            'licence' => $licenceId
        ];

        $licence = $this->$provider();
        $licence->shouldReceive('getId')->andReturn(123);

        $dto = Qry::create($data);

        $this->setIsValid('canAccessCompanySubsidiary', [111], false);
        $this->setIsValid('canAccessLicence', [123], true);

        $this->assertEquals(false, $this->sut->isValid($dto));
    }

    /**
     * @dataProvider licenceProviderProvider
     */
    public function testIsValidWithContextAndOwnership($applicationId, $licenceId, $provider)
    {
        $data = [
            'id' => 111,
            'application' => $applicationId,
            'licence' => $licenceId
        ];

        $dto = Qry::create($data);

        $licence = $this->$provider();
        $licence->shouldReceive('getId')->andReturn(123);

        $companySubsidiary = m::mock(\Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary::class);
        $companySubsidiary->shouldReceive('getLicence')->andReturn($licence);

        $mockCsRepo = $this->mockRepo('CompanySubsidiary');
        $mockCsRepo->shouldReceive('fetchByIds')->with([111])->andReturn([$companySubsidiary]);

        $this->setIsValid('canAccessCompanySubsidiary', [111], true);
        $this->setIsValid('canAccessLicence', [123], true);

        $this->assertEquals(true, $this->sut->isValid($dto));
    }

    /**
     * @dataProvider licenceProviderProvider
     */
    public function testIsValidWithContextAndOwnershipWithoutMatching($applicationId, $licenceId, $provider)
    {
        $data = [
            'id' => 111,
            'application' => $applicationId,
            'licence' => $licenceId
        ];

        $dto = Qry::create($data);

        $lic = $this->$provider();
        $lic->shouldReceive('getId')->andReturn(123);

        $licence = m::mock(Licence::class);

        $companySubsidiary = m::mock(\Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary::class);
        $companySubsidiary->shouldReceive('getLicence')->andReturn($licence);

        $mockCsRepo = $this->mockRepo('CompanySubsidiary');
        $mockCsRepo->shouldReceive('fetchByIds')->with([111])->andReturn([$companySubsidiary]);

        $this->setIsValid('canAccessCompanySubsidiary', [111], true);
        $this->setIsValid('canAccessLicence', [123], true);

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
}
