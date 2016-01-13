<?php

/**
 * Get a list of Transport Manager Licences and Transport Manager Applications
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\TmResponsibilities;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * Get a list of Transport Manager Licences and Transport Manager Applications
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmResponsibilitiesList extends AbstractQueryHandler
{
    protected $repoServiceName = 'TransportManagerLicence';

    protected $extraRepos = ['TransportManagerApplication'];

    public function handleQuery(QueryInterface $query)
    {
        $repo = $this->getRepo();

        $tmLicences = $repo->fetchForTransportManager($query->getTransportManager(), $query->getLicenceStatuses());
        $tmApplications = $this->getRepo('TransportManagerApplication')
            ->fetchForTransportManager($query->getTransportManager(), $query->getApplicationStatuses(), true);
        return [
            'result' => $tmLicences,
            'count' => count($tmLicences),
            'tmApplications' => $tmApplications,
            'tmApplicationsCount' => count($tmApplications)
        ];
    }
}
