<?php

/**
 * Operating Centres
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\InspectionRequest;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Operating Centres
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OperatingCentres extends AbstractQueryHandler
{
    protected $repoServiceName = 'InspectionRequest';

    protected $extraRepos = ['Application', 'Licence'];

    public function handleQuery(QueryInterface $query)
    {
        if ($query->getType() === 'licence') {
            $licence = $this->getRepo('Licence')->fetchWithOperatingCentres($query->getIdentifier());
            $operatingCentres = $licence->getOcForInspectionRequest();
        } else {
            $application = $this->getRepo('Application')->fetchWithLicenceAndOc($query->getIdentifier());
            $operatingCentres = $application->getOcForInspectionRequest();
        }
        return [
            'result' => $operatingCentres,
            'count' => count($operatingCentres)
        ];
    }
}
