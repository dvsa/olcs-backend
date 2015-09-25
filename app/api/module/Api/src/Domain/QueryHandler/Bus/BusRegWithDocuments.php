<?php

/**
 * Bus
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bus;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Bus
 */
class BusRegWithDocuments extends AbstractQueryHandler
{
    protected $repoServiceName = 'Bus';

    public function handleQuery(QueryInterface $query)
    {
        $busReg = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $busReg,
            [
                'licence' => [
                    'organisation' => ['disqualifications'],
                    'licenceType',
                    'status',
                ],
                'busNoticePeriod',
                'busServiceTypes',
                'trafficAreas',
                'localAuthoritys',
                'subsidised',
                'otherServices',
                'variationReasons',
                'npPublicationNo',
                //'documents'
            ],
            [
                'npPublicationNo' => $busReg->getLicence()->determineNpNumber(),
                //'documents' => $busReg->fetchDocumentsByLocalAuthority($query->getLocalAuthority())
            ]
        );
    }
}
