<?php


namespace Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section;


use Dvsa\Olcs\Api\Entity\Surrender;

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
        $items = [];
        if ($surrender->getLicence()->getLicenceType()->getId() === Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL) {
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
        }
        return [
            'multiItems' => [
                $items
            ]
        ];
    }
}
