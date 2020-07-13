<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\ApplyRanges;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\ApplyRanges\ForCpWithNoCountriesProvider;
use Dvsa\Olcs\Api\Service\Permits\ApplyRanges\UnrestrictedWithLowestStartNumberProvider;
use Dvsa\Olcs\Api\Service\Permits\ApplyRanges\RestrictedWithFewestCountriesProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

/**
 * ForCpWithNoCountriesProviderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ForCpWithNoCountriesProviderTest extends MockeryTestCase
{
    private $result;

    private $range1;

    private $range2;

    private $range3;

    private $ranges;

    private $unrestrictedWithLowestStartNumberProvider;

    private $restrictedWithFewestCountriesProvider;

    private $forCpWithNoCountriesProvider;

    public function setUp(): void
    {
        $this->result = new Result();

        $this->range1 = [
            'entity' => m::mock(IrhpPermitRange::class),
            'countryIds' => ['IT', 'RU'],
            'emissionsCategory' => RefData::EMISSIONS_CATEGORY_EURO6_REF,
            'permitsRemaining' => 20
        ];

        $range2Entity = m::mock(IrhpPermitRange::class);
        $range2Entity->shouldReceive('getId')
            ->andReturn(45);
        $range2Entity->shouldReceive('getFromNo')
            ->andReturn(400);

        $this->range2 = [
            'entity' => $range2Entity,
            'countryIds' => ['IT', 'RU'],
            'emissionsCategory' => RefData::EMISSIONS_CATEGORY_EURO5_REF,
            'permitsRemaining' => 20
        ];

        $this->range3 = [
            'entity' => m::mock(IrhpPermitRange::class),
            'countryIds' => ['ES'],
            'emissionsCategory' => RefData::EMISSIONS_CATEGORY_EURO5_REF,
            'permitsRemaining' => 25
        ];

        $this->ranges = [$this->range1, $this->range2, $this->range3];

        $this->unrestrictedWithLowestStartNumberProvider = m::mock(
            UnrestrictedWithLowestStartNumberProvider::class
        );

        $this->restrictedWithFewestCountriesProvider = m::mock(
            RestrictedWithFewestCountriesProvider::class
        );

        $this->forCpWithNoCountriesProvider = new ForCpWithNoCountriesProvider(
            $this->unrestrictedWithLowestStartNumberProvider,
            $this->restrictedWithFewestCountriesProvider
        );
    }

    public function testSelectRangeFromRestrictedWithFewestCountries()
    {
        $this->unrestrictedWithLowestStartNumberProvider->shouldReceive('getRange')
            ->with($this->ranges)
            ->once()
            ->andReturn(null);

        $this->restrictedWithFewestCountriesProvider->shouldReceive('getRanges')
            ->with($this->ranges)
            ->andReturn([$this->range2]);

        $this->assertEquals(
            $this->range2,
            $this->forCpWithNoCountriesProvider->selectRange($this->result, $this->ranges)
        );

        $expectedMessages = [
            '    - no unrestricted ranges available, use restricted range with fewest countries',
            '    - using restricted range with fewest countries: id 45 with countries IT, RU'
        ];

        $this->assertEquals(
            $expectedMessages,
            $this->result->getMessages()
        );
    }

    public function testExceptionOnNoRestrictedWithFewestCountries()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Assertion failed in method %s::selectRange: count($ranges) == 0',
                ForCpWithNoCountriesProvider::class
            )
        );

        $this->unrestrictedWithLowestStartNumberProvider->shouldReceive('getRange')
            ->with($this->ranges)
            ->once()
            ->andReturn(null);

        $this->restrictedWithFewestCountriesProvider->shouldReceive('getRanges')
            ->with($this->ranges)
            ->andReturn([]);

        $this->forCpWithNoCountriesProvider->selectRange($this->result, $this->ranges);
    }

    public function testExceptionOnMultipleRestrictedWithFewestCountries()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Assertion failed in method %s::selectRange: count($ranges) > 1',
                ForCpWithNoCountriesProvider::class
            )
        );

        $this->unrestrictedWithLowestStartNumberProvider->shouldReceive('getRange')
            ->with($this->ranges)
            ->once()
            ->andReturn(null);

        $this->restrictedWithFewestCountriesProvider->shouldReceive('getRanges')
            ->with($this->ranges)
            ->andReturn([$this->range1, $this->range3]);

        $this->forCpWithNoCountriesProvider->selectRange($this->result, $this->ranges);
    }

    public function testSelectRangeFromUnrestrictedWithLowestStartNumber()
    {
        $this->unrestrictedWithLowestStartNumberProvider->shouldReceive('getRange')
            ->with($this->ranges)
            ->once()
            ->andReturn($this->range2);

        $this->assertEquals(
            $this->range2,
            $this->forCpWithNoCountriesProvider->selectRange($this->result, $this->ranges)
        );

        $expectedMessages = [
            '    - using unrestricted range with lowest start number: id 45 starts at 400'
        ];

        $this->assertEquals(
            $expectedMessages,
            $this->result->getMessages()
        );
    }
}
