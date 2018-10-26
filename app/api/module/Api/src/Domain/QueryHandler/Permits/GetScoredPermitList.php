<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Transfer\Query\IrhpCandidatePermit\GetScoredList as Query;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\Sectors;

/**
 * Get a list of scored irhp candidate permit records and associated data
 */
class GetScoredPermitList extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    const DEVOLVED_ADMINISTRATION_TRAFFIC_AREAS = ['M', 'G', 'N'];

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'IrhpCandidatePermit';
    protected $bundledRepos = [
        'irhpPermitApplication' => [
            'ecmtPermitApplication' => [
                'countrys',
                'sectors',
                'internationalJourneys'
            ],
            'irhpPermitWindow',
            'licence' => [
                'trafficArea',
                'organisation'
            ]
        ],
        'irhpPermitRange' => [
            'countrys'
        ],
    ];

    /**
     * Return a list of scored irhp candidate permit records and associated data
     * @param QueryInterface|Query $query DTO
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var Query $query */
        $results = $this->getRepo('IrhpCandidatePermit')->fetchAllScoredForStock($query->getStockId());

        return [
            'result' => $this->formatResults(
                $this->resultList(
                    $results,
                    $this->bundledRepos
                )
            )
        ];
    }

    /**
     * Format the results of the query fetchAllScoredForStock to make them more readable
     *
     * @param array $data an array of query results with the same
     *                  format as that returned by fetchAllScoredForStock
     *
     * @return array a formatted and mapped array
     */
    private function formatResults(array $data)
    {
        $formattedData = [];

        foreach ($data as $row) {
            $sector = $row['irhpPermitApplication']['ecmtPermitApplication']['sectors'];
            $trafficArea = $row['irhpPermitApplication']['licence']['trafficArea'];
            $interJourneys = $row['irhpPermitApplication']['ecmtPermitApplication']['internationalJourneys']['id'];
            $licence = $row['irhpPermitApplication']['licence'];

            $devolvedAdministration = 'N/A';
            if (in_array($trafficArea['id'], self::DEVOLVED_ADMINISTRATION_TRAFFIC_AREAS)) {
                $devolvedAdministration = $trafficArea['name'];
            }

            $formattedData[] = [
                'Permit Ref'                        => $row['irhpPermitApplication']['ecmtPermitApplication']['applicationRef'] . ' / ' . $row['id'],
                'Operator'                          => $licence['organisation']['name'],
                'Application Score'                 => $row['applicationScore'],
                'Permit Intensity of Use'           => $row['intensityOfUse'],
                'Random Factor'                     => $row['randomFactor'],
                'Randomised Permit Score'           => $row['randomizedScore'],
                'Percentage International'          => EcmtPermitApplication::INTERNATIONAL_JOURNEYS_DECIMAL_MAP[$interJourneys],
                'Sector'                            => $sector['name'] === Sectors::SECTOR_OPTION_NAME__NONE ? 'N/A' : $sector['name'],
                'Devolved Administration'           => $devolvedAdministration,
                'Result'                            => $row['successful'] ? 'Successful' : 'Unsuccessful',
                'Restricted Countries - Requested'  => $this->getRestrictedCountriesRequested($row),
                'Restricted Countries - Offered'    => $this->getRestrictedCountriesOffered($row)
            ];
        }

        return $formattedData;
    }

    /**
     * Retrieves the list of restricted countries requested for display in an export .csv file
     *
     * @param array $row Row from data from query
     *
     * @return string
     */
    private function getRestrictedCountriesRequested(array $row)
    {
        if ($row['irhpPermitApplication']['ecmtPermitApplication']['hasRestrictedCountries']) {
            return $this->formatRestrictedCountriesForDisplay($row['irhpPermitApplication']['ecmtPermitApplication']['countrys']);
        }

        return 'N/A';
    }

    /**
     * Retrieves the list of restricted countries offered for display in an export .csv file
     *
     * @param array $row Row from data from query
     *
     * @return string
     */
    private function getRestrictedCountriesOffered(array $row)
    {
        if (count($row['irhpPermitRange']['countrys']) > 0) {
            return $this->formatRestrictedCountriesForDisplay($row['irhpPermitRange']['countrys']);
        }

        return 'N/A';
    }

    /**
     * Formats a given list of restricted countries for display in an export .csv file
     *
     * @param array $countries a list of restricted countries in the format returned by backend
     *
     * @return string a list of countries seperated by semicolons
     */
    private function formatRestrictedCountriesForDisplay(array $countries)
    {
        return implode('; ', array_column($countries, 'countryDesc'));
    }
}
