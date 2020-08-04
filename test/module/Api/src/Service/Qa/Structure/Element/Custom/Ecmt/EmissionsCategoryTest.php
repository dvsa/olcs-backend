<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt\EmissionsCategory;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * EmissionsCategoryTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EmissionsCategoryTest extends MockeryTestCase
{
    public function testGetRepresentation()
    {
        $name = 'euro5Required';
        $labelTranslationKey = 'qanda.ecmt.number-of-permits.label.euro5';
        $value = '45';
        $maxValue = '62';

        $expectedRepresentation = [
            'name' => $name,
            'labelTranslationKey' => $labelTranslationKey,
            'value' => $value,
            'maxValue' => $maxValue
        ];

        $emissionsCategory = new EmissionsCategory($name, $labelTranslationKey, $value, $maxValue);

        $this->assertSame(
            $expectedRepresentation,
            $emissionsCategory->getRepresentation()
        );
    }
}
