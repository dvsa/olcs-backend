<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\ApplyRanges;

use Dvsa\Olcs\Api\Service\Permits\ApplyRanges\RestrictedCountryIdsProvider;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * RestrictedCountryIdsProviderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RestrictedCountryIdsProviderTest extends MockeryTestCase
{
    public function testGetIds()
    {
        $restrictedCountryIds = ['AT', 'GR', 'HU', 'IT', 'RU'];

        $restrictedCountryIdsProvider = new RestrictedCountryIdsProvider($restrictedCountryIds);

        $this->assertEquals(
            $restrictedCountryIds,
            $restrictedCountryIdsProvider->getIds()
        );
    }
}
