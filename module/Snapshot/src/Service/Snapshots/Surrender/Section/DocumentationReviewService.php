<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section;

use Dvsa\Olcs\Api\Entity\Surrender;

class DocumentationReviewService extends AbstractReviewService
{
    /**
     * @param Surrender $surrender
     * @return array|mixed
     */
    public function getConfigFromData(Surrender $surrender)
    {
        return [
            'multiItems' => [
                [
                    [
                        'label' => 'surrender-review-documentation-operator-licence',
                        'value' => $surrender->getLicenceDocumentStatus()->getDescription()
                    ],
                    [
                        'label' => 'surrender-review-additional-information',
                        'value' => $surrender->getLicenceDocumentInfo()
                    ],
                    [
                        'label' => 'surrender-review-documentation-community-licence',
                        'value' => $surrender->getCommunityLicenceDocumentStatus()->getDescription()
                    ],
                    [
                        'label' => 'surrender-review-additional-information',
                        'value' => $surrender->getCommunityLicenceDocumentInfo()
                    ],
                ]
            ]
        ];
    }
}
