<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\CertRoadworthiness;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\CertRoadworthiness\MotExpiryDateGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Common\DateWithThreshold;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Common\DateWithThresholdGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * MotExpiryDateGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class MotExpiryDateGeneratorTest extends MockeryTestCase
{
    public function testGenerate()
    {
        $elementGeneratorContext = m::mock(ElementGeneratorContext::class);

        $dateWithThreshold = m::mock(DateWithThreshold::class);

        $dateWithThresholdGenerator = m::mock(DateWithThresholdGenerator::class);
        $dateWithThresholdGenerator->shouldReceive('generate')
            ->with($elementGeneratorContext, 'P13M')
            ->once()
            ->andReturn($dateWithThreshold);

        $motExpiryDateGenerator = new MotExpiryDateGenerator($dateWithThresholdGenerator);

        $this->assertSame(
            $dateWithThreshold,
            $motExpiryDateGenerator->generate($elementGeneratorContext)
        );
    }
}
