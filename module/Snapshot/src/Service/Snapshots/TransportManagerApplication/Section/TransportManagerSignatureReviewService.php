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
            Organisation::ORG_TYPE_LLP => 'directors-signature',
            Organisation::ORG_TYPE_REGISTERED_COMPANY => 'directors-signature',
            Organisation::ORG_TYPE_PARTNERSHIP => 'partners-signature',
            Organisation::ORG_TYPE_SOLE_TRADER => 'owners-signature',
        ];

        $organisationType = $tma->getApplication()->getLicence()->getOrganisation()->getType()->getId();

        if (isset($map[$organisationType])) {
            $label = $map[$organisationType];
        } else {
            $label = 'responsible-person-signature';
        }

        return $this->translate($label);
    }
}
