<?php

namespace Dvsa\OlcsTest\Api\Service\Helper;

use Dvsa\Olcs\Api\Service\Helper\FormatAddress;
use Dvsa\OlcsTest\Api\Service\Helper\Stub\AddressFormatterAwareTraitStub;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * @covers Dvsa\Olcs\Api\Service\Helper\AddressFormatterAwareTrait
 */
class AddressFormatterAwareTraitTest extends MockeryTestCase
{
    public function testGetSet()
    {
        /** @var FormatAddress $mockFormatter */
        $mockFormatter = m::mock(FormatAddress::class);

        $sut = new AddressFormatterAwareTraitStub();
        $sut->setAddressFormatter($mockFormatter);

        static::assertSame($mockFormatter, $sut->getAddressFormatter());
    }
}
