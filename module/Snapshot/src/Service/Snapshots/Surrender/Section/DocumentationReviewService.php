<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section;

use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Api\Entity\System\RefData;

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
                array_merge($this->getLicenceConfig($surrender), $this->getCommunityLicenceConfig($surrender))
            ]
        ];
    }


    protected function getLicenceConfig(Surrender $surrender): array
    {
        $status = $surrender->getLicenceDocumentStatus();
        $config = [
            [
                'label' => 'surrender-review-documentation-operator-licence',
                'value' => $status->getDescription()
            ]
        ];

        if ($status->getId() !== RefData::SURRENDER_DOC_STATUS_DESTROYED) {
            $config[] = [
                    'label' => 'surrender-review-additional-information',
                    'value' => $surrender->getLicenceDocumentInfo()
            ];
        }
        return $config;
    }

    protected function getCommunityLicenceConfig(Surrender $surrender): array
    {
        $status = $surrender->getCommunityLicenceDocumentStatus();
        $config = [
            [
                'label' => 'surrender-review-documentation-community-licence',
                'value' => $status->getDescription()
            ]
        ];

        if ($status->getId() !== RefData::SURRENDER_DOC_STATUS_DESTROYED) {
            $config[] = [
                'label' => 'surrender-review-additional-information',
                'value' => $surrender->getCommunityLicenceDocumentInfo()
            ];
        }
        return $config;
    }
}
