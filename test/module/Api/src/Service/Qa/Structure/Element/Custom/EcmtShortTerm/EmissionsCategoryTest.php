<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\EmissionsCategory;
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
        $labelTranslationKey = 'qanda.ecmt-short-term.number-of-permits.label.euro5';
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
