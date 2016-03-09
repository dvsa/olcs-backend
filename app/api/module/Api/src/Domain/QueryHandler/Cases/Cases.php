<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Cases
 */
final class Cases extends AbstractQueryHandler
{
    protected $repoServiceName = 'Cases';

    public function handleQuery(QueryInterface $query)
    {
        $case = $this->getRepo()->fetchUsingId($query);

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
            ]
        );
    }
}
