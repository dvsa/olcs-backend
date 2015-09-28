<?php

/**
 * Bus
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bus;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;

/**
 * Bus
 */
class BusRegWithDocuments extends AbstractQueryHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Bus';

    public function handleQuery(QueryInterface $query)
    {
        /** @var BusReg $busReg */
        $busReg = $this->getRepo()->fetchUsingId($query);

        $currentUser = $this->getCurrentUser();

        $txcInboxEntries = null;

        if ($this->isGranted('selfserve-ebsr-documents')) {
            $txcInboxEntries = $this->resultList(
                $busReg->fetchLatestUnreadBusRegDocumentsByLocalAuthority(
                    $currentUser->getLocalAuthority()
                )
            );
        }

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
                'npPublicationNo'
            ],
            [
                'npPublicationNo' => $busReg->getLicence()->determineNpNumber(),
                'txcInboxEntries' => $txcInboxEntries
            ]
        );
    }
}
