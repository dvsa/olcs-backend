<?php

/**
 * TxcInbox List
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bus\Ebsr;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\TxcInbox as Repository;
use Doctrine\ORM\Query as DoctrineQuery;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;

/**
 * TxcInboxList
 * This QueryHandler will query one of two tables.
 * Either the TxcInbox table (for LAs) or the EbsrSubmission table (operators/organisation users).
 */
class TxcInboxList extends AbstractQueryHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'TxcInbox';
    protected $extraRepos = ['EbsrSubmission'];

    /**
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var Repository $repo */
        $repo = $this->getRepo();

        $currentUser = $this->getCurrentUser();

        $localAuthority = $currentUser->getLocalAuthority();
        $organisation = $this->getCurrentOrganisation();

        if (empty($localAuthority) && $organisation instanceof Organisation) {
            $txcInboxEntries = $this->getRepo('EbsrSubmission')->fetchListForOrganisation(
                $organisation,
                $query->getEbsrSubmissionType(),
                $query->getEbsrSubmissionStatus()
            );
        } else {
            $txcInboxEntries = $repo->fetchUnreadListForLocalAuthority(
                $localAuthority,
                $query->getEbsrSubmissionType(),
                $query->getEbsrSubmissionStatus()
            );
        }
        return [
            'result' => $this->resultList(
                $txcInboxEntries,
                [
                    'busReg' => [
                        'ebsrSubmissions' => [
                            'ebsrSubmissionType',
                            'ebsrSubmissionStatus'
                        ],
                        'licence' => [
                            'organisation'
                        ],
                        'otherServices'
                    ]
                ]
            ),
            'count' => count($txcInboxEntries)
        ];
    }
}
