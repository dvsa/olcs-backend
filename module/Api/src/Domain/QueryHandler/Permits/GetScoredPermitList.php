<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Api\Service\Permits\Scoring\ScoringQueryProxy;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Transfer\Query\IrhpCandidatePermit\GetScoredList as Query;
use Dvsa\Olcs\Api\Entity\Permits\Sectors;
use Dvsa\Olcs\Api\Entity\Permits\Traits\CandidatePermitCreationTrait;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Get a list of scored irhp candidate permit records and associated data
 */
class GetScoredPermitList extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait, CandidatePermitCreationTrait;

    const DEVOLVED_ADMINISTRATION_TRAFFIC_AREAS = [
        TrafficArea::SCOTTISH_TRAFFIC_AREA_CODE,
        TrafficArea::WELSH_TRAFFIC_AREA_CODE,
        TrafficArea::NORTHERN_IRELAND_TRAFFIC_AREA_CODE
    ];

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'Country';

    protected $extraRepos = ['IrhpPermitRange'];

    /** @var ScoringQueryProxy */
    private $scoringQueryProxy;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->scoringQueryProxy = $mainServiceLocator->get('PermitsScoringScoringQueryProxy');

        return parent::createService($serviceLocator);
    }

    /**
     * Return a list of scored irhp candidate permit records and associated data
     * @param QueryInterface|Query $query DTO
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $formattedData = [];

        $stockId = $query->getStockId();
        $countryIdsByRangeId = $this->getCountryIdsByRangeIdLookup($stockId);
        $countryIdsByApplicationId = $this->getCountryIdsByApplicationIdLookup($stockId);
        $countryNamesById = $this->getCountryNamesByIdLookup();
        $internationalJourneysDecimalMap = $this->getInternationalJourneysDecimalMap();

        $rows = $this->scoringQueryProxy->fetchScoringReport($stockId);

        foreach ($rows as $row) {
            $permitReference = $row['licenceNo'] . ' / ' . $row['applicationId'] . ' / ' . $row['candidatePermitId'];

            $devolvedAdministrationName = 'N/A';
            if (in_array($row['trafficAreaId'], self::DEVOLVED_ADMINISTRATION_TRAFFIC_AREAS)) {
                $devolvedAdministrationName = $row['trafficAreaName'];
            }

            $percentageInternationalName = $internationalJourneysDecimalMap[$row['applicationInternationalJourneys']];

            $applicationSectorName = $row['applicationSectorName'];
            if ($row['applicationSectorName'] == Sectors::SECTOR_OPTION_NAME__NONE) {
                $applicationSectorName = 'N/A';
            }

            $applicationId = $row['applicationId'];
            $countryNamesRequestedCsv = '';
            if (isset($countryIdsByApplicationId[$applicationId])) {
                $countryNamesRequestedCsv = $this->generateCountryNamesCsv(
                    $countryIdsByApplicationId[$applicationId],
                    $countryNamesById
                );
            }

            $candidatePermitRangeId = $row['candidatePermitRangeId'];
            $countryNamesOfferedCsv = '';
            if (isset($countryIdsByRangeId[$candidatePermitRangeId])) {
                $countryNamesOfferedCsv = $this->generateCountryNamesCsv(
                    $countryIdsByRangeId[$candidatePermitRangeId],
                    $countryNamesById
                );
            }

            $successCaption = $row['candidatePermitSuccessful'] ? 'Successful' : 'Unsuccessful';

            $formattedRow = [
                'Permit Ref'                        => $permitReference,
                'Operator'                          => $row['organisationName'],
                'Application Score'                 => $row['candidatePermitApplicationScore'],
                'Permit Intensity of Use'           => $row['candidatePermitIntensityOfUse'],
                'Random Factor'                     => $row['candidatePermitRandomFactor'],
                'Randomised Permit Score'           => $row['candidatePermitRandomizedScore'],
                'Requested Emissions Category'      => $row['candidatePermitRequestedEmissionsCategory'],
                'Assigned Emissions Category'       => $row['candidatePermitAssignedEmissionsCategory'],
                'Percentage International'          => $percentageInternationalName,
                'Sector'                            => $applicationSectorName,
                'Devolved Administration'           => $devolvedAdministrationName,
                'Result'                            => $successCaption,
                'Restricted Countries - Requested'  => $countryNamesRequestedCsv,
                'Restricted Countries - Offered'    => $countryNamesOfferedCsv
            ];

            $formattedData[] = $formattedRow;
        }

        return ['result' => $formattedData];
    }

    /**
     * Returns a semicolon separated list of country names
     *
     * @param array $countryNamesById a lookup table of country codes to names
     * @param array $countryCodes an array of country codes
     *
     * @return string
     */
    private function generateCountryNamesCsv(array $countryCodes, array $countryNamesById)
    {
        $countryNames = [];
        foreach ($countryCodes as $countryCode) {
            $countryNames[] = $countryNamesById[$countryCode];
        }

        return implode('; ', $countryNames);
    }

    /**
     * Return an array with indexes containing range ids and values containing the associated country codes
     *
     * @param int stockId
     *
     * @return array
     */
    private function getCountryIdsByRangeIdLookup($stockId)
    {
        return $this->getCountryIdLookup(
            $this->getRepo('IrhpPermitRange')->fetchRangeIdToCountryIdAssociations($stockId),
            'rangeId'
        );
    }

    /**
     * Return an array with indexes containing ecmt application ids and values containing the associated country codes
     *
     * @param int stockId
     *
     * @return array
     */
    private function getCountryIdsByApplicationIdLookup($stockId)
    {
        return $this->getCountryIdLookup(
            $this->scoringQueryProxy->fetchApplicationIdToCountryIdAssociations($stockId),
            'applicationId'
        );
    }

    /**
     * Return an unflattened list of associations between an entity and the associated country codes
     *
     * @param array $associations an array containing entity ids and country codes
     * @param string $entityIdIndex the key in the associations array containing the entity id
     *
     * @return array
     */
    private function getCountryIdLookup(array $associations, $entityIdIndex)
    {
        $countryIdsByEntity = [];

        foreach ($associations as $association) {
            $entityId = $association[$entityIdIndex];
            $countryId = $association['countryId'];

            if (isset($countryIdsByEntity[$entityId])) {
                $countryIdsByEntity[$entityId][] = $countryId;
            } else {
                $countryIdsByEntity[$entityId] = [$countryId];
            }
        }

        return $countryIdsByEntity;
    }

    /**
     * Return an array with country codes as keys and country names as values
     *
     * @return array
     */
    private function getCountryNamesByIdLookup()
    {
        $countries = $this->getRepo()->fetchIdsAndDescriptions();

        $countriesLookup = [];
        foreach ($countries as $country) {
            $countriesLookup[$country['countryId']] = $country['description'];
        }

        return $countriesLookup;
    }
}
