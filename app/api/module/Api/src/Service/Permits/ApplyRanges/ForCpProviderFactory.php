<?php

namespace Dvsa\Olcs\Api\Service\Permits\ApplyRanges;

class ForCpProviderFactory
{
    /**
     * Create instance of ForCpProvider with the specified restricted countries list
     *
     * @param array $restrictedCountryIds
     *
     * @return ForCpProvider
     */
    public function create(array $restrictedCountryIds)
    {
        $restrictedCountryIdsProvider = new RestrictedCountryIdsProvider($restrictedCountryIds);

        $restrictedRangesProvider = new RestrictedRangesProvider();

        $unrestrictedWithLowestStartNumberProvider = new UnrestrictedWithLowestStartNumberProvider();

        $restrictedWithFewestCountriesProvider = new RestrictedWithFewestCountriesProvider(
            $restrictedRangesProvider
        );

        $withFewestNonRequestedCountriesProvider = new WithFewestNonRequestedCountriesProvider(
            $restrictedCountryIdsProvider
        );

        $restrictedWithMostMatchingCountriesProvider = new RestrictedWithMostMatchingCountriesProvider(
            $restrictedRangesProvider
        );

        $forCpWithCountriesAndNoMatchingRangesProvider = new ForCpWithCountriesAndNoMatchingRangesProvider(
            $unrestrictedWithLowestStartNumberProvider,
            $restrictedWithFewestCountriesProvider
        );

        $forCpWithCountriesAndMultipleMatchingRangesProvider = new ForCpWithCountriesAndMultipleMatchingRangesProvider(
            $withFewestNonRequestedCountriesProvider
        );

        $forCpWithCountriesProvider = new ForCpWithCountriesProvider(
            $restrictedWithMostMatchingCountriesProvider,
            $forCpWithCountriesAndNoMatchingRangesProvider,
            $forCpWithCountriesAndMultipleMatchingRangesProvider
        );

        $forCpWithNoCountriesProvider = new ForCpWithNoCountriesProvider(
            $unrestrictedWithLowestStartNumberProvider,
            $restrictedWithFewestCountriesProvider
        );

        $entityIdsExtractor = new EntityIdsExtractor();

        $rangeSubsetGenerator = new RangeSubsetGenerator();

        return new ForCpProvider(
            $forCpWithCountriesProvider,
            $forCpWithNoCountriesProvider,
            $entityIdsExtractor,
            $rangeSubsetGenerator
        );
    }
}
