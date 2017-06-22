<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\ContinuationDetail;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail as ContinuationDetailEntity;

/**
 * Licence Checklist for continuation
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceChecklist extends AbstractQueryHandler
{
    protected $repoServiceName = 'ContinuationDetail';

    public function handleQuery(QueryInterface $query)
    {
        /** @var ContinuationDetailEntity $continuationDetail */
        $continuationDetail = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $continuationDetail,
            [
                'licence' => [
                    'licenceType',
                    'status',
                    'goodsOrPsv',
                    'trafficArea',
                    'organisation' => [
                        'type',
                        'organisationPersons' => [
                            'person' => [
                                'title'
                            ]
                        ]
                    ],
                    'tradingNames'
                ]
            ]
        );
    }
}
