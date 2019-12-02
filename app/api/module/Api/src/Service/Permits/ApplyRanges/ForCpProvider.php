<?php

namespace Dvsa\Olcs\Api\Service\Permits\ApplyRanges;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;

class ForCpProvider
{
    /** @var ForCpWithCountriesProvider */
    private $forCpWithCountriesProvider;

    /** @var ForCpWithNoCountriesProvider */
    private $forCpWithNoCountriesProvider;

    /** @var EntityIdsExtractor */
    private $entityIdsExtractor;

    /** @var RangeSubsetGenerator */
    private $rangeSubsetGenerator;

    /**
     * Create service instance
     *
     * @param ForCpWithCountriesProvider $forCpWithCountriesProvider
     * @param ForCpWithNoCountriesProvider $forCpWithNoCountriesProvider
     * @param EntityIdsExtractor $entityIdsExtractor
     * @param RangeSubsetGenerator $rangeSubsetGenerator
     *
     * @return ForCpProvider
     */
    public function __construct(
        ForCpWithCountriesProvider $forCpWithCountriesProvider,
        ForCpWithNoCountriesProvider $forCpWithNoCountriesProvider,
        EntityIdsExtractor $entityIdsExtractor,
        RangeSubsetGenerator $rangeSubsetGenerator
    ) {
        $this->forCpWithCountriesProvider = $forCpWithCountriesProvider;
        $this->forCpWithNoCountriesProvider = $forCpWithNoCountriesProvider;
        $this->entityIdsExtractor = $entityIdsExtractor;
        $this->rangeSubsetGenerator = $rangeSubsetGenerator;
    }

    /**
     * Returns the best fitting range for an application with the specified countries
     *
     * @param Result $result
     * @param IrhpCandidatePermit $irhpCandidatePermit
     * @param array $ranges
     *
     * @return IrhpPermitRange
     */
    public function selectRange(
        Result $result,
        IrhpCandidatePermit $candidatePermit,
        array $ranges
    ) {
        $applicationCountries = $candidatePermit->getIrhpPermitApplication()
            ->getRelatedApplication()
            ->getCountrys()
            ->getValues();

        $rangeSubset = $this->rangeSubsetGenerator->generate($candidatePermit, $ranges);

        if (count($applicationCountries) > 0) {
            $applicationCountryIds = $this->entityIdsExtractor->getExtracted($applicationCountries);

            $result->addMessage('    - has one or more countries: ' . implode(', ', $applicationCountryIds));

            $range = $this->forCpWithCountriesProvider->selectRange(
                $result,
                $rangeSubset,
                $applicationCountryIds
            );
        } else {
            $result->addMessage('    - has no countries');
            $range = $this->forCpWithNoCountriesProvider->selectRange($result, $rangeSubset);
        }

        return $range['entity'];
    }
}
