<?php

/**
 * Application Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\ApplicationOperatingCentre;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre as ApplicationOperatingCentreEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;

/**
 * Application Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationOperatingCentre extends AbstractQueryHandler
{
    protected $repoServiceName = 'ApplicationOperatingCentre';

    public function handleQuery(QueryInterface $query)
    {
        /** @var ApplicationOperatingCentreEntity $aoc */
        $aoc = $this->getRepo()->fetchUsingId($query);

        $application = $aoc->getApplication();

        return $this->result(
            $aoc,
            [
                'operatingCentre' => [
                    'address' => [
                        'countryCode'
                    ],
                    'adDocuments'
                ]
            ],
            [
                'isPsv' => $application->isPsv(),
                'canUpdateAddress' => $application->isNew(),
                'wouldIncreaseRequireAdditionalAdvertisement' => $application->isVariation(),
                'currentVehiclesRequired' => $this->getNoOfVehiclesRequired($aoc, $application->getLicence()),
                'currentTrailersRequired' => $this->getNoOfTrailersRequired($aoc, $application->getLicence())
            ]
        );
    }

    protected function getNoOfVehiclesRequired(ApplicationOperatingCentreEntity $aoc, Licence $licence)
    {
        if ($aoc->getAction() !== 'U') {
            return null;
        }

        /** @var LicenceOperatingCentre $loc */
        $loc = $this->getRepo('ApplicationOperatingCentre')->findCorrespondingLoc($aoc, $licence);
        return $loc->getNoOfVehiclesRequired();
    }

    protected function getNoOfTrailersRequired(ApplicationOperatingCentreEntity $aoc, Licence $licence)
    {
        if ($aoc->getAction() !== 'U') {
            return null;
        }

        /** @var LicenceOperatingCentre $loc */
        $loc = $this->getRepo('ApplicationOperatingCentre')->findCorrespondingLoc($aoc, $licence);
        return $loc->getNoOfTrailersRequired();
    }
}
