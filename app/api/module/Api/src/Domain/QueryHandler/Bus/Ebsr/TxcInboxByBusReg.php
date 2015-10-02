<?php

/**
 * Txc Inbox by bus reg
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Repository\TxcInbox as Repository;
use Dvsa\OlcsTest\Api\Entity\Bus as BusEntity;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;

/**
 * Txc Inbox by bus reg
 */
class TxcInboxByBusReg extends AbstractQueryHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'TxcInbox';

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

        if (!isset($txcInboxResults[0]) || !($txcInboxResults[0]->getBusReg() instanceof BusEntity)) {
            throw new NotFoundException();
        }

        $txcInbox = $txcInboxResults[0];

        return $this->result(
            $txcInbox,
            [
                'pdfDocument',
                'routeDocument',
                'zipDocument'
            ],
            [
                'busReg' => $this->result(
                    $txcInbox->getBusReg(),
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
                        'npPublicationNo' => $txcInbox->getBusReg()->getLicence()->determineNpNumber()
                    ]
                )->serialize(),
            ]
        );
    }
}
