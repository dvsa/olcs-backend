<?php

/**
 * Txc Inbox by bus reg
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Repository\TxcInbox as Repository;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\User\Permission;

/**
 * Txc Inbox by bus reg
 */
class TxcInboxByBusReg extends AbstractQueryHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'TxcInbox';

    protected $extraRepos = ['Bus'];

    public function handleQuery(QueryInterface $query)
    {
        /** @var Repository $repo */
        $repo = $this->getRepo();

        $currentUser = $this->getCurrentUser();

        $localAuthority = $currentUser->getLocalAuthority();

        $localAuthorityId = null;
        if ($localAuthority instanceof \Dvsa\Olcs\Api\Entity\Bus\LocalAuthority) {
            $localAuthorityId = $localAuthority->getId();
        }

        // relationship is 1:n to allow multiple entries for different local authorities.
        // However the actual result for a look up will always be a 1:1 providing the local authority filter
        // is applied
        $txcInboxResults = $repo->fetchListForLocalAuthorityByBusReg($query->getBusReg(), $localAuthorityId);

        if (!isset($txcInboxResults[0]) || !($txcInboxResults[0]->getBusReg() instanceof BusRegEntity)) {
            // alternative command to fetch the bus reg details only
            $busReg = $this->getRepo('Bus')->fetchById($query->getBusReg());

            // since no results are found on TxcInbox table, return an empty entity with no documents
            $txcInbox = new TxcInbox();
        } else {
            $txcInbox = $txcInboxResults[0];
            $busReg = $txcInbox->getBusReg();
        }

        if (!isset($busReg) || !($busReg instanceof BusRegEntity)) {
            throw new NotFoundException();
        }

        return $this->result(
            $txcInbox,
            [
                'pdfDocument',
                'routeDocument',
                'zipDocument'
            ],
            [
                'busReg' => $this->result(
                    $busReg,
                    [
                        'status',
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
                        'npPublicationNo'
                    ],
                    [
                        'npPublicationNo' => $busReg->getLicence()->determineNpNumber()
                    ]
                )->serialize(),
            ]
        );

        return $this->result(
            $txcInbox,
            [
                'txcDocuments'
            ],
            [
                'txcDocuments' => [
                    'pdfDocument' => $txcInbox->getPdfDocument(),
                    'routeDocument' => $txcInbox->getRouteDocument(),
                    'zipDocument' => $txcInbox->getZipDocument(),
                ],
                'busReg' => $this->result(
                    $busReg,
                    [
                        'status',
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
                        'variationReasons'
                    ]
                )->serialize(),
            ]
        );
    }
}
