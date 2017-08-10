<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Operating Centres Continuation Review Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OperatingCentresReviewService extends AbstractReviewService
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
        $locs = $continuationDetail->getLicence()->getOperatingCentres();
        $isGoods =
            $continuationDetail->getLicence()->getGoodsOrPsv()->getId() === Licence::LICENCE_CATEGORY_GOODS_VEHICLE;

        $header = [
            [
                ['value' => 'continuations.oc-section.table.name', 'header' => true],
                ['value' => 'continuations.oc-section.table.vehicles', 'header' => true],
            ]
        ];
        if ($isGoods) {
            $header[0][] = ['value' => 'continuations.oc-section.table.trailers', 'header' => true];
        }

        $config = [];
        /** @var LicenceOperatingCentre $lv */
        foreach ($locs as $loc) {
            /** @var OperatingCentre $oc */
            $oc = $loc->getOperatingCentre();
            $address = $oc->getAddress();
            $row = [
                ['value' => implode(', ', [$address->getAddressLine1(), $address->getTown()])],
                ['value' => $loc->getNoOfVehiclesRequired()]
            ];
            if ($isGoods) {
                $row[] = ['value' => $loc->getNoOfTrailersRequired()];
            }
            $config[] = $row;

        }
        usort(
            $config,
            function ($a, $b) {
                return strcmp($a[0]['value'], $b[0]['value']);
            }
        );

        return array_merge($header, $config);
    }

    /**
     * Get summary from data
     *
     * @param ContinuationDetail $continuationDetail continuation detail
     *
     * @return array
     */
    public function getSummaryFromData(ContinuationDetail $continuationDetail)
    {
        $licence = $continuationDetail->getLicence();

        $summary = [
            [
                ['value' => 'continuations.oc-section.table.vehicles', 'header' => true],
                ['value' => $licence->getTotAuthVehicles()]
            ],
            [
                ['value' => 'continuations.oc-section.table.trailers', 'header' => true],
                ['value' => $licence->getTotAuthTrailers()]
            ]
        ];

        return $summary;
    }

    /**
     * Get summary header
     *
     * @return string
     */
    public function getSummaryHeader()
    {
        return 'continuations.oc-section.table.authorisation';
    }
}
