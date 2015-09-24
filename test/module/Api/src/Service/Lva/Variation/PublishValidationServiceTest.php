<?php

namespace Dvsa\OlcsTest\Api\Service\Lva\Variation;

use Mockery as m;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Service\Lva\Variation\PublishValidationService;

/**
 * PublishValidationServiceTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class PublishValidationServiceTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\Variation\PublishValidationService
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new PublishValidationService();
    }

    public function testValidate()
    {
        /* @var $application ApplicationEntity  */
        $application = m::mock(ApplicationEntity::class)->makePartial();

        $application->shouldReceive('hasActiveS4')->with()->once()->andReturn(false);
        $application->shouldReceive('isPublishable')->with()->once()->andReturn(true);

        $result = $this->sut->validate($application);

        $this->assertEmpty($result);
    }

    public function testValidateAllFail()
    {
        /* @var $application ApplicationEntity  */
        $application = m::mock(ApplicationEntity::class)->makePartial();

        $application->shouldReceive('hasActiveS4')->with()->once()->andReturn(true);
        $application->shouldReceive('isPublishable')->with()->once()->andReturn(false);

        $result = $this->sut->validate($application);

        $this->assertCount(2, $result);
        $this->assertArrayHasKey(PublishValidationService::ERROR_S4, $result);
        $this->assertArrayHasKey(PublishValidationService::ERROR_NOT_PUBLISHABLE, $result);
    }
}
