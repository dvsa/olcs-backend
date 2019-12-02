<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\EcmtRemoval;

use DateInterval;
use DateTime;
use Dvsa\Olcs\Api\Service\Qa\Common\CurrentDateTimeFactory;
use Dvsa\Olcs\Api\Service\Qa\Common\DateIntervalFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtRemoval\PermitStartDateFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtRemoval\PermitStartDateGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date\DateGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date\Date as DateElement;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * PermitStartDateGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class PermitStartDateGeneratorTest extends MockeryTestCase
{
    public function testGenerate()
    {
        $elementGeneratorContext = m::mock(ElementGeneratorContext::class);

        $dateInterval = m::mock(DateInterval::class);

        $dateIntervalFactory = m::mock(DateIntervalFactory::class);
        $dateIntervalFactory->shouldReceive('create')
            ->with('P60D')
            ->once()
            ->andReturn($dateInterval);

        $currentDateTime = m::mock(DateTime::class);
        $currentDateTime->shouldReceive('add')
            ->with($dateInterval)
            ->once()
            ->globally()
            ->ordered();

        $currentDateTimeFactory = m::mock(CurrentDateTimeFactory::class);
        $currentDateTimeFactory->shouldReceive('create')
            ->withNoArgs()
            ->once()
            ->andReturn($currentDateTime);

        $dateElement = m::mock(DateElement::class);

        $dateGenerator = m::mock(DateGenerator::class);
        $dateGenerator->shouldReceive('generate')
            ->with($elementGeneratorContext)
            ->once()
            ->andReturn($dateElement);

        $permitStartDate = m::mock(PermitStartDate::class);

        $permitStartDateFactory = m::mock(PermitStartDateFactory::class);
        $permitStartDateFactory->shouldReceive('create')
            ->with($currentDateTime, $dateElement)
            ->once()
            ->globally()
            ->ordered()
            ->andReturn($permitStartDate);

        $permitStartDateGenerator = new PermitStartDateGenerator(
            $permitStartDateFactory,
            $currentDateTimeFactory,
            $dateIntervalFactory,
            $dateGenerator
        );

        $this->assertSame(
            $permitStartDate,
            $permitStartDateGenerator->generate($elementGeneratorContext)
        );
    }
}
