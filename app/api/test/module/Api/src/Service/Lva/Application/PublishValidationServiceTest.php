<?php

namespace Dvsa\OlcsTest\Api\Service\Lva\Application;

use Mockery as m;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Service\Lva\Application\PublishValidationService;

/**
 * PublishValidationServiceTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class PublishValidationServiceTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\Application\PublishValidationService
     */
    protected $sut;

    /**
     * @var \Mockery\MockInterface
     */
    protected $feesHelperService;

    public function setUp(): void
    {
        $this->feesHelperService = m::mock();

        $sm = m::mock(\Laminas\ServiceManager\ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('FeesHelperService')->once()->andReturn($this->feesHelperService);

        $this->sut = (new PublishValidationService())->createService($sm);
    }

    public function testValidate()
    {
        /* @var $application ApplicationEntity  */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(2409);

        $applicationCompletion = new \Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion($application);
        $applicationCompletion->setOperatingCentresStatus(2);

        $application->shouldReceive('getApplicationCompletion')->with()->once()->andReturn($applicationCompletion);
        $this->feesHelperService
            ->shouldReceive('getOutstandingFeesForApplication')
            ->with(2409, true)
            ->once()
            ->andReturn([]);
        $application->shouldReceive('isPublishable')->with()->once()->andReturn(true);

        $result = $this->sut->validate($application);

        $this->assertEmpty($result);
    }

    public function testValidateAllFail()
    {
        /* @var $application ApplicationEntity  */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(2409);
        $application->setLicenceType(new RefData(Licence::LICENCE_TYPE_STANDARD_NATIONAL));

        $applicationCompletion = new \Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion($application);
        $applicationCompletion->setOperatingCentresStatus(1);
        $applicationCompletion->setTransportManagersStatus(0);

        $application->shouldReceive('getApplicationCompletion')->with()->once()->andReturn($applicationCompletion);
        $this->feesHelperService->shouldReceive('getOutstandingFeesForApplication')->with(2409, true)->once()
            ->andReturn(['foo']);
        $application->shouldReceive('isPublishable')->with()->once()->andReturn(false);

        $result = $this->sut->validate($application);

        $this->assertCount(4, $result);
        $this->assertArrayHasKey(PublishValidationService::ERROR_MUST_COMPETE_OC, $result);
        $this->assertArrayHasKey(PublishValidationService::ERROR_MUST_COMPETE_TM, $result);
        $this->assertArrayHasKey(PublishValidationService::ERROR_OUSTANDING_FEE, $result);
        $this->assertArrayHasKey(PublishValidationService::ERROR_NOT_PUBLISHABLE, $result);
    }
}
