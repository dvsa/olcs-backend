<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt\EmissionsCategory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt\NoOfPermits;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * NoOfPermitsTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsTest extends MockeryTestCase
{
    public function testGetRepresentation()
    {
        $year = 2017;
        $maxPermitted = 42;

        $emissionsCategory1Representation = [
            'name' => 'emissionsCategory1Name',
            'labelTranslationKey' => 'emissionsCategory1LabelTranslationKey',
            'value' => 'emissionsCategory1Value',
            'maxValue' => 'emissionsCategory1MaxValue'
        ];

        $emissionsCategory1 = m::mock(EmissionsCategory::class);
        $emissionsCategory1->shouldReceive('getRepresentation')
            ->andReturn($emissionsCategory1Representation);

        $emissionsCategory2Representation = [
            'name' => 'emissionsCategory2Name',
            'labelTranslationKey' => 'emissionsCategory2LabelTranslationKey',
            'value' => 'emissionsCategory2Value',
            'maxValue' => 'emissionsCategory2MaxValue'
        ];

        $emissionsCategory2 = m::mock(EmissionsCategory::class);
        $emissionsCategory2->shouldReceive('getRepresentation')
            ->andReturn($emissionsCategory2Representation);

        $noOfPermits = new NoOfPermits($year, $maxPermitted);
        $noOfPermits->addEmissionsCategory($emissionsCategory1);
        $noOfPermits->addEmissionsCategory($emissionsCategory2);

        $expectedRepresentation = [
            'year' => $year,
            'maxPermitted' => $maxPermitted,
            'emissionsCategories' => [
                $emissionsCategory1Representation,
                $emissionsCategory2Representation
            ]
        ];

        $this->assertEquals(
            $expectedRepresentation,
            $noOfPermits->getRepresentation()
        );
    }
}
