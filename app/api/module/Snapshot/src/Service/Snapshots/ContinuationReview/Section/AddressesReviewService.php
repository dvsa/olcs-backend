<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Snapshot\Service\Formatter\Address;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Addresses Continuation Review Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class AddressesReviewService extends AbstractReviewService
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
        $correspondenceCd = $licence->getCorrespondenceCd();
        $establishmentCd = $licence->getEstablishmentCd();

        $columns = [
            'addressFields' => ['addressLine1', 'addressLine2', 'addressLine3', 'addressLine4', 'town', 'postcode']
        ];
        $config = [
            [
                ['value' => 'continuation-review-addresses-correspondence-address'],
                ['value' => Address::format($correspondenceCd->getAddress(), $columns), 'header' => true]
            ]
        ];
        if ($establishmentCd !== null) {
            $config[] = [
                ['value' => 'continuation-review-addresses-establishment-address'],
                ['value' => Address::format($establishmentCd->getAddress(), $columns), 'header' => true]
            ];
        } else {
            $config[] = [
                ['value' => 'continuation-review-addresses-establishment-address'],
                ['value' => 'continuation-review-addresses-establishment-address-same', 'header' => true]
            ];
        }
        $primaryNumber =  $correspondenceCd->getPhoneContactNumber(RefData::PHONE_NUMBER_PRIMARY_TYPE);
        if ($primaryNumber !== null) {
            $config[] = [
                ['value' => 'continuation-review-addresses-primary-number'],
                ['value' => $primaryNumber, 'header' => true]
            ];
        }
        $secondaryNumber = $correspondenceCd->getPhoneContactNumber(RefData::PHONE_NUMBER_SECONDARY_TYPE);
        if ($secondaryNumber !== null) {
            $config[] = [
                ['value' => 'continuation-review-addresses-secondary-number'],
                ['value' => $secondaryNumber, 'header' => true]
            ];
        }

        return $config;
    }
}
