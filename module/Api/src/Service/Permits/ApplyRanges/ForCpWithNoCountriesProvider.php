<?php

namespace Dvsa\Olcs\Api\Service\Permits\ApplyRanges;

use Dvsa\Olcs\Api\Domain\Command\Result;
use RuntimeException;

class ForCpWithNoCountriesProvider
{
    /** @var UnrestrictedWithLowestStartNumberProvider */
    private $unrestrictedWithLowestStartNumberProvider;

    /** @var RestrictedWithFewestCountriesProvider */
    private $restrictedWithFewestCountriesProvider;

    /** @var HighestAvailabilityRangeSelector */
    private $highestAvailabilityRangeSelector;

    /**
     * Create service instance
     *
     * @param UnrestrictedWithLowestStartNumberProvider $unrestrictedWithLowestStartNumberProvider
     * @param RestrictedWithFewestCountriesProvider $restrictedWithFewestCountriesProvider
     * @param HighestAvailabilityRangeSelector $highestAvailabilityRangeSelector
     *
     * @return ForCpWithNoCountriesProvider
     */
    public function __construct(
        UnrestrictedWithLowestStartNumberProvider $unrestrictedWithLowestStartNumberProvider,
        RestrictedWithFewestCountriesProvider $restrictedWithFewestCountriesProvider,
        HighestAvailabilityRangeSelector $highestAvailabilityRangeSelector
    ) {
        $this->unrestrictedWithLowestStartNumberProvider = $unrestrictedWithLowestStartNumberProvider;
        $this->restrictedWithFewestCountriesProvider = $restrictedWithFewestCountriesProvider;
        $this->highestAvailabilityRangeSelector = $highestAvailabilityRangeSelector;
    }

    /**
     * Returns the best fitting range for an candidate permit that requests no countries
     *
     * @param Result $result
     * @param array $ranges
     *
     * @return array
     */
    public function selectRange(Result $result, array $ranges)
    {
        $range = $this->unrestrictedWithLowestStartNumberProvider->getRange($ranges);

        if (is_null($range)) {
            $result->addMessage('    - no unrestricted ranges available, use restricted range with fewest countries');

            $ranges = $this->restrictedWithFewestCountriesProvider->getRanges($ranges);
            switch (count($ranges)) {
                case 0:
                    throw new RuntimeException(
                        'Assertion failed in method ' . __METHOD__ . ': count($ranges) == 0'
                    );
                case 1:
                    $range = $ranges[0];
                    break;
                default:
                    $result->addMessage('    - multiple ranges have the fewest countries');
                    return $this->highestAvailabilityRangeSelector->getRange($result, $ranges);
            }

            $message = sprintf(
                '    - using restricted range with fewest countries: id %d with countries %s',
                $range['entity']->getId(),
                implode(', ', $range['countryIds'])
            );
        } else {
            $rangeEntity = $range['entity'];

            $message = sprintf(
                '    - using unrestricted range with lowest start number: id %d starts at %d',
                $rangeEntity->getId(),
                $rangeEntity->getFromNo()
            );
        }

        $result->addMessage($message);

        return $range;
    }
}
