<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Note\Note as NoteEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
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

        $this->auditRead($case);

        $latestNote = $this->getLatestNoteByCase($case);

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
                        'filter' => function ($element) {
                            return in_array(
                                (string)$element->getPublicationSection(),
                                [
                                    PublicationSectionEntity::APP_NEW_SECTION,
                                    PublicationSectionEntity::VAR_NEW_SECTION,
                                ]
                            );
                        },
                        'publication'
                    )
                ),
                'tmDecisions',
                'erruRequest' => array(
                    'msiType'
                )
            ],
            [
                'latestNote' => $latestNote
            ]
        );
    }

    /**
     * Logic is to query the notes table by the foreign key determined by the case type and not use the note
     * type except in the event of no foreign key's present.
     *
     * @param CasesEntity $case
     * @return string
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function getLatestNoteByCase(CasesEntity $case)
    {
        switch ($case->getCaseType()) {
            case $case::LICENCE_CASE_TYPE:
            case $case::IMPOUNDING_CASE_TYPE:
                $licenceId = $case->getLicence()->getId();
                return $this->getRepo('Note')->fetchForOverview($licenceId);
            case $case::APP_CASE_TYPE:
                $licenceId = $case->getApplication()->getLicence()->getId();
                return $this->getRepo('Note')->fetchForOverview($licenceId);
            case $case::TM_CASE_TYPE:
                $tmId = $case->getTransportManager()->getId();
                return $this->getRepo('Note')->fetchForOverview(null, null, $tmId);
            default:
                return $this->getRepo('Note')->fetchForOverview(null, null, null, NoteEntity::NOTE_TYPE_CASE);
        }
    }
}
