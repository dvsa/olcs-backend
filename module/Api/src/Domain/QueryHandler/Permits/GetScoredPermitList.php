<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use DateTime;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Transfer\Query\IrhpCandidatePermit\GetScoredList as Query;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;

/**
 * Get a list of scored irhp candidate permit records
 * and associated data
 */
class GetScoredPermitList extends AbstractQueryHandler
{
    const DEVOLVED_ADMINISTRATION_TRAFFIC_AREAS = ['M', 'G', 'N'];

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
     * Return a list of scored irhp candidate permit records
     * and associated data
     * @param QueryInterface|Query $query DTO
     *
     * @return array
     * @throws RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var Query $query */
        /** @var IrhpCandidatePermit $repo */
        $repo = $this->getRepo();

        $results = $repo->fetchAllScoredForStock(
            $query->getStockId()
        );

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
     * Format the results of the query fetchAllScoredForStock
     * to make them more readable
     *
     * @param array $results an array of query results with the same
     *                  format as that returned by fetchAllScoredForStock
     *
     * @return array a formatted and mapped array
     * @todo: find dynamic sector name for the 'None' option instead of hardcoding it
     */
    private function formatResults($data)
    {
        $formattedData = array();
        foreach ($data as $row) {
            $sector = $row['irhpPermitApplication']['ecmtPermitApplication']['sectors'];

            $formattedData[] = [
                'permitRef'                     => $row['irhpPermitApplication']['licence']['licNo'] . '/' . $row['irhpPermitApplication']['id'] . '/' . $row['id'],
                'organisation'                  => $row['irhpPermitApplication']['licence']['organisation']['name'],
                'applicationScore'              => $row['applicationScore'],
                'intensityOfUse'                => $row['intensityOfUse'],
                'randomFactor'                  => $row['randomFactor'],
                'randomizedScore'               => $row['randomizedScore'],
                'internationalJourneys'         => EcmtPermitApplication::INTERNATIONAL_JOURNEYS_DECIMAL_MAP[$row['irhpPermitApplication']['ecmtPermitApplication']['internationalJourneys']['id']],
                'sector'                        => $sector['name'] === 'None/More than one of these sectors' ? 'N/A' : $sector['name'],
                'devolvedAdministration'        => in_array(
                    $row['irhpPermitApplication']['licence']['trafficArea']['id'],
                    self::DEVOLVED_ADMINISTRATION_TRAFFIC_AREAS
                ) ? $row['irhpPermitApplication']['licence']['trafficArea']['name'] : 'N/A',
                'result'                        => $row['successful'] ? 'Successful' : 'Unsuccessful',
                'restrictedCountriesRequested'  => self::getRestrictedCountriesRequested($row),
                'restrictedCountriesOffered'    => self::getRestrictedCountriesOffered($row)
            ];
        }

        return ['results' => $formattedData];
    }

    /**
     * Retrieves the list of restricted countries requested
     * for display in an export .csv file
     *
     * @param array $data Row from data from query
     *
     * @return string
     */
    private static function getRestrictedCountriesRequested($row)
    {
        if ($row['irhpPermitApplication']['ecmtPermitApplication']['hasRestrictedCountries']) {
            return self::formatRestrictedCountriesForDisplay($row['irhpPermitApplication']['ecmtPermitApplication']['countrys']);
        }

        return 'N/A';
    }

    /**
     * Retrieves the list of restricted countries offered
     * for display in an export .csv file
     *
     * @param array $data Row from data from query
     *
     * @return string
     */
    private static function getRestrictedCountriesOffered($row)
    {
        if (count($row['irhpPermitRange']['countrys']) > 0) {
            return self::formatRestrictedCountriesForDisplay($row['irhpPermitRange']['countrys']);
        }

        return 'N/A';
    }

    /**
     * Formats a given list of restricted countries
     * for display in an export .csv file
     *
     * @param array a list of restricted countries in the format returned by backend
     *
     * @return string a list of countries seperated by semicolons
     */
    private static function formatRestrictedCountriesForDisplay($countries)
    {
        $restrictedCountries = '';
        foreach ($countries as $country) {
            $restrictedCountries = $restrictedCountries . '; ' . $country['countryDesc'];
        }

        return substr($restrictedCountries, 2); //remove the first ;
    }
}
