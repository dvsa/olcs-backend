<?php

/**
 * Licence Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\LicenceOperatingCentre;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre as LicenceOperatingCentreEntity;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Licence Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceOperatingCentre extends AbstractQueryHandler
{
    protected $repoServiceName = 'LicenceOperatingCentre';

    public function handleQuery(QueryInterface $query)
    {
        /** @var LicenceOperatingCentreEntity $loc */
        $loc = $this->getRepo()->fetchUsingId($query);

        $licence = $loc->getLicence();

        return $this->result(
            $loc,
            [
                'operatingCentre' => [
                    'address' => [
                        'countryCode'
                    ],
                    'adDocuments'
                ]
            ],
            [
                'isPsv' => $licence->isPsv(),
                'canUpdateAddress' => $this->canUpdateAddress($query),
                'wouldIncreaseRequireAdditionalAdvertisement' => $query->getIsVariation(),
                'currentVehiclesRequired' => $loc->getNoOfVehiclesRequired(),
                'currentTrailersRequired' => $loc->getNoOfTrailersRequired()
            ]
        );
    }

    protected function canUpdateAddress($query)
    {
        if ($this->isGranted(Permission::INTERNAL_USER)) {
            return true;
        }

        return !$query->getIsVariation();
    }
}
