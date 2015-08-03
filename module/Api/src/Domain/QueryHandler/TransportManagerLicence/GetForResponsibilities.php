<?php

/**
 * Get a Transport Manager Licence for Responsibilities page
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\TransportManagerLicence;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Get a Transport Manager Licence for Responsibilities page
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GetForResponsibilities extends AbstractQueryHandler
{
    protected $repoServiceName = 'TransportManagerLicence';

    protected $extraRepos = [
        'OtherLicence'
    ];

    public function handleQuery(QueryInterface $query)
    {
        $transportManagerLicence = $this->getRepo()->fetchForResponsibilities($query->getId());
        $otherLicences = $this->getRepo('OtherLicence')->fetchForTransportManagerLicence($query->getId());
        return [
            'result'        => $transportManagerLicence,
            'count'         => count($transportManagerLicence),
            'otherLicences' => $otherLicences
        ];
    }
}
