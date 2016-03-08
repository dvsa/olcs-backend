<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases;

use Common\RefData;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Note\Note as NoteEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Cases
 */
final class Cases extends AbstractQueryHandler
{
    protected $repoServiceName = 'Cases';

    protected $extraRepos = ['Note'];

    public function handleQuery(QueryInterface $query)
    {
        /** @var CasesEntity $case */
        $case = $this->getRepo()->fetchUsingId($query);

        $latestNote = $this->getLatestNoteByCase($case);

        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->in('publicationSection', [1, 3])
        );

        // @todo look at simplifying
        return $this->result(
            $case,
            [
                'outcomes',
                'categorys',
                'appeal' => ['outcome'],
                'stays' => ['outcome', 'stayType'],
                'legacyOffences',
                'transportManager' => array(
                    'homeCd' => array(
                        'person'
                    )
                ),
                'licence' => array(
                    'licenceType',
                    'status',
                    'trafficArea',
                    'establishmentCd' => array(
                        'address'
                    ),
                    'organisation' => array(
                        'type',
                        'disqualifications',
                        'tradingNames',
                        'organisationPersons' => array(
                            'person' => array(
                                'contactDetails'
                            )
                        ),
                        'contactDetails' => array(
                            'address'
                        )
                    )
                ),
                'application' => array(
                    'operatingCentres',
                    'publicationLinks' => array(
                        'criteria' => $criteria,
                        'publication'
                    )
                ),
                'tmDecisions',
                'erruRequest'
            ],
            [
                'latestNote' => $latestNote
            ]
        );
    }

    /**
     * @param CasesEntity $case
     * @return string
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function getLatestNoteByCase(CasesEntity $case)
    {
        $noteType = $case->getNoteType();
        $latestNote = '';

        if (!empty($noteType)) {
            $licenceId = null;
            $licence = $case->getLicence();
            if (!empty($licence)) {
                $licenceId = $licence->getId();
            }

            $latestNote = $this->getRepo('Note')->fetchForOverview($licenceId, null, $noteType);
        }

        return $latestNote;
    }
}
