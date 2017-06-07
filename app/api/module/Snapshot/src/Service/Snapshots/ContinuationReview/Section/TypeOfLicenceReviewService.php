<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Type Of Licence Continuation Review Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TypeOfLicenceReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param ContinuationDetail $continuationDetail continuation detail
     *
     * @return array
     */
    public function getConfigFromData(ContinuationDetail $continuationDetail)
    {
        /** @var Licence $licence */
        $licence = $continuationDetail->getLicence();

        $config = [
            [
                ['value' => 'continuation-review-operator-location'],
                ['value' => $this->getOperatorLocation($licence), 'header' => true]
            ]
        ];

        if ($licence->getNiFlag() !== 'Y') {
            $config[] = [
                ['value' => 'continuation-review-operator-type'],
                ['value' => $this->getOperatorType($licence), 'header' => true]
            ];
        }

        $config[] = [
            ['value' => 'continuation-review-licence-type'],
            ['value' => $licence->getLicenceType()->getDescription(), 'header' => true]
        ];

        return $config;
    }

    /**
     * Get operator location
     *
     * @param Licence $licence licence
     *
     * @return string
     */
    private function getOperatorLocation($licence)
    {
        return $licence->getNiFlag() === 'N'
            ? 'Great Britain'
            : 'Northern Ireland';
    }

    /**
     * Get operator type
     *
     * @param Licence $licence licence
     *
     * @return string
     */
    private function getOperatorType($licence)
    {
        return $licence->isGoods() ? 'Goods' : 'PSV';
    }
}
