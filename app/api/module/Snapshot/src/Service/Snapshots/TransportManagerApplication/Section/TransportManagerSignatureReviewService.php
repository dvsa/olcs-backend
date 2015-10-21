<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section;

use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

/**
 * Transport Manager Responsibility Review Service
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerSignatureReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param TransportManagerApplication $tma
     *
     * @return array
     */
    public function getConfig(TransportManagerApplication $tma)
    {
        return [
            'markup' => $this->translateReplace(
                'markup-tma-declaration-signature',
                [$this->getOwnerLabel($tma), $this->translate('tm-review-return-address')]
            )
        ];
    }

    /**
     * Get the the label for the owner signature box
     *
     * @param TransportManagerApplication $tma
     *
     * @return string
     */
    private function getOwnerLabel(TransportManagerApplication $tma)
    {
        $map = [
            Organisation::ORG_TYPE_LLP => 'Director\'s signature',
            Organisation::ORG_TYPE_REGISTERED_COMPANY => 'Director\'s signature',
            Organisation::ORG_TYPE_PARTNERSHIP => 'Partner\'s signature',
            Organisation::ORG_TYPE_SOLE_TRADER => 'Owner\'s signature',
        ];

        $organisationType = $tma->getApplication()->getLicence()->getOrganisation()->getType()->getId();

        if (isset($map[$organisationType])) {
            return $map[$organisationType];
        }

        return 'A responsible person\'s signature';
    }
}
