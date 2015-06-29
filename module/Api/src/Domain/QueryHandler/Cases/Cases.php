<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
                'appeals',
                'stays',
                'legacyOffences',
                'transportManager',
                'licence' => array(
                    'trafficArea',
                    'establishmentCd' => array(
                        'address'
                    ),
                    'organisation' => array(
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
                'tmDecisions'
            ]
        );
    }
}
