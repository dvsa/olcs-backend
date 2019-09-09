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
        $restrictedCountryIdsProvider = new RestrictedCountryIdsProvider();

        $this->assertEquals(
            ['AT', 'GR', 'HU', 'IT', 'RU'],
            $restrictedCountryIdsProvider->getIds()
        );
    }
}
