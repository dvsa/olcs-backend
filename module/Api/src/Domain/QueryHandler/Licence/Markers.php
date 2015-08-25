<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Get the the markers to display for a Licence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class Markers extends AbstractQueryHandler
{
    protected $repoServiceName = 'Licence';
    protected $extraRepos = ['ContinuationDetail'];

    public function handleQuery(QueryInterface $query)
    {
        /* @var $licence \Dvsa\Olcs\Api\Entity\Licence\Licence */
        $licence = $this->getRepo()->fetchUsingId($query);
        $continuationDetail = $this->getContinuationDetail($licence);
        $continuationDetailResponse = ($continuationDetail) ?
            $this->result($continuationDetail, ['continuation', 'licence'])->serialize() :
            null;

        return $this->result(
            $licence,
            [
                'licenceStatusRules' => ['licenceStatus'],
                'organisation' => ['disqualifications'],
                'cases' => [
                    'appeal' => ['outcome'],
                    'stays' => ['outcome', 'stayType']
                ],
            ],
            [
                'continuationMarker' => $continuationDetailResponse,
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
