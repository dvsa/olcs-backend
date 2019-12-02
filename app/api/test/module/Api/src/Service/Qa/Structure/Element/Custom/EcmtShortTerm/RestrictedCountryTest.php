<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\RestrictedCountry;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * RestrictedCountryTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RestrictedCountryTest extends MockeryTestCase
{
    public function testGetRepresentation()
    {
        $code = 'GR';
        $labelTranslationKey = 'Greece';
        $checked = true;

        $expectedRepresentation = [
            'code' => $code,
            'labelTranslationKey' => $labelTranslationKey,
            'checked' => $checked
        ];

        $restrictedCountry = new RestrictedCountry($code, $labelTranslationKey, $checked);

        $this->assertEquals(
            $expectedRepresentation,
            $restrictedCountry->getRepresentation()
        );
    }
}
