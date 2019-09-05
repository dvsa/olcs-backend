<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\ApplyRanges;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Service\Permits\ApplyRanges\ForCpWithCountriesAndMultipleMatchingRangesProvider;
use Dvsa\Olcs\Api\Service\Permits\ApplyRanges\WithFewestNonRequestedCountriesProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

/**
 * ForCpWithCountriesAndMultipleMatchingRangesProviderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ForCpWithCountriesAndMultipleMatchingRangesProviderTest extends MockeryTestCase
{
    private $range1;

    private $range2;

    private $range3;

    private $ranges;

    private $applicationCountryIds;

    private $result;

    private $withFewestNonRequestedCountriesProvider;

    private $rangesProvider;

    public function setUp()
    {
        $rangeEntity1Id = 47;
        $rangeEntity1 = m::mock(IrhpPermitRange::class);
        $rangeEntity1->shouldReceive('getId')
            ->andReturn($rangeEntity1Id);

        $rangeEntity2Id = 49;
        $rangeEntity2 = m::mock(IrhpPermitRange::class);
        $rangeEntity2->shouldReceive('getId')
            ->andReturn($rangeEntity2Id);

        $rangeEntity3Id = 55;
        $rangeEntity3 = m::mock(IrhpPermitRange::class);
        $rangeEntity3->shouldReceive('getId')
            ->andReturn($rangeEntity3Id);

        $this->range1 = [
            'entity' => $rangeEntity1,
            'countryIds' => ['RU', 'IT']
        ];

        $this->range2 = [
            'entity' => $rangeEntity2,
            'countryIds' => ['AT', 'GR']
        ];

        $this->range3 = [
            'entity' => $rangeEntity3,
            'countryIds' => ['GR', 'AT']
        ];

        $this->ranges = [$this->range1, $this->range2, $this->range3];

        $this->applicationCountryIds = ['AT', 'RU'];

        $this->result = new Result();

        $this->withFewestNonRequestedCountriesProvider = m::mock(WithFewestNonRequestedCountriesProvider::class);

        $this->rangesProvider = new ForCpWithCountriesAndMultipleMatchingRangesProvider(
            $this->withFewestNonRequestedCountriesProvider
        );
    }

    public function testSelectRange()
    {
        $matchingRanges = [$this->range2];

        $this->withFewestNonRequestedCountriesProvider->shouldReceive('getRanges')
            ->with($this->applicationCountryIds, $this->ranges)
            ->andReturn($matchingRanges);
    
        $this->assertEquals(
            $this->range2,
            $this->rangesProvider->selectRange($this->result, $this->ranges, $this->applicationCountryIds)
        );

        $this->assertEquals(
            [
                '    - more than one range found with most matching countries:',
                '      - range with id 47 and countries RU, IT',
                '      - range with id 49 and countries AT, GR',
                '      - range with id 55 and countries GR, AT',
                '    - range 49 with countries AT, GR has the fewest non-requested countries'
            ],
            $this->result->getMessages()
        );
    }

    public function testExceptionOnMultipleMatchingRanges()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Assertion failed in method %s::selectRange: count($matchingRanges) > 1',
                ForCpWithCountriesAndMultipleMatchingRangesProvider::class
            )
        );

        $matchingRanges = [$this->range2, $this->range3];

        $this->withFewestNonRequestedCountriesProvider->shouldReceive('getRanges')
            ->with($this->applicationCountryIds, $this->ranges)
            ->andReturn($matchingRanges);

        $this->rangesProvider->selectRange($this->result, $this->ranges, $this->applicationCountryIds);
    }
}
