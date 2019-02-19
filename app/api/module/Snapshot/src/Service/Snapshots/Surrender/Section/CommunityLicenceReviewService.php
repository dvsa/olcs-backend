<?php


namespace Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Api\Entity\System\RefData;

class CommunityLicenceReviewService extends AbstractReviewService
{

    /**
     * Format the readonly config from the given record
     *
     * @param Surrender $surrender
     *
     * @return mixed
     */
    public function getConfigFromData(Surrender $surrender)
    {
        $items[] =
            [
                'label' => 'surrender-review-documentation-community-licence',
                'value' => $surrender->getCommunityLicenceDocumentStatus()->getDescription()
            ];

        if ($surrender->getCommunityLicenceDocumentStatus()->getId() !== RefData::SURRENDER_DOC_STATUS_DESTROYED) {
            $items[] =
                [
                    'label' => 'surrender-review-additional-information',
                    'value' => $surrender->getCommunityLicenceDocumentInfo()
                ];
        }
        return [
            'multiItems' => [
                $items
            ]
        ];
    }
}
