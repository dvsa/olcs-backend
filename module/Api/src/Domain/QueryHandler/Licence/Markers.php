<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Note\Note as NoteEntity;

/**
 * Get the the markers to display for a Licence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class Markers extends AbstractQueryHandler
{
    protected $repoServiceName = 'Licence';
    protected $extraRepos = ['ContinuationDetail', 'Note'];

    public function handleQuery(QueryInterface $query)
    {
        /* @var $licence \Dvsa\Olcs\Api\Entity\Licence\Licence */
        $licence = $this->getRepo()->fetchUsingId($query);
        $continuationDetail = $this->getContinuationDetail($licence);
        $continuationDetailResponse = ($continuationDetail) ?
            $this->result($continuationDetail, ['continuation', 'licence'])->serialize() :
            null;

        $latestNote = $this->getRepo('Note')->fetchForOverview($query->getId(), null, NoteEntity::NOTE_TYPE_CASE);

        return $this->result(
            $licence,
            [
                'licenceStatusRules' => ['licenceStatus'],
                'organisation' => [
                    'type',
                    'disqualifications',
                    'organisationPersons' => [
                        'person' => [
                            'contactDetails' => ['disqualifications']
                        ]
                    ]
                ],
                'cases' => [
                    'appeal' => ['outcome'],
                    'stays' => ['outcome', 'stayType']
                ],
            ],
            [
                'continuationMarker' => $continuationDetailResponse,
                'latestNote' => $latestNote
            ]
        );
    }

    /**
     * Get a Continuation Detail for the marker
     *
     * @param \Dvsa\Olcs\Api\Entity\Licence\Licence $licence
     *
     * @return \Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail|null
     */
    private function getContinuationDetail(\Dvsa\Olcs\Api\Entity\Licence\Licence $licence)
    {
        $continuationDetails = $this->getRepo('ContinuationDetail')->fetchForLicence($licence->getId());
        if (count($continuationDetails) > 0) {
            return $continuationDetails[0];
        }

        return null;
    }
}
