<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;

/**
 * Goods Operating Centre Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsOperatingCentreReviewService extends AbstractReviewService
{
    /**
     * Format the OC config
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $config = $this->getServiceLocator()->get('Review\PsvOperatingCentre')->getConfigFromData($data);

        $config['multiItems']['vehicles+trailers'][] = [
            'label' => 'review-operating-centre-total-trailers',
            'value' => $data['noOfTrailersRequired']
        ];

        $adPlacedValue = [
            ApplicationOperatingCentre::AD_POST => 'review-operating-centre-advertisement-post',
            ApplicationOperatingCentre::AD_UPLOAD_NOW => 'review-operating-centre-advertisement-upload-now',
            ApplicationOperatingCentre::AD_UPLOAD_LATER => 'review-operating-centre-advertisement-upload-later'
        ];
        // Add the advertisements fields
        $config['multiItems']['advertisements'] = [
            [
                'label' => 'review-operating-centre-advertisement-ad-placed',
                'value' => $this->translate($adPlacedValue[$data['adPlaced']])
            ]
        ];

        if ($data['adPlaced'] === ApplicationOperatingCentre::AD_UPLOAD_NOW) {
            $config['multiItems']['advertisements'] = array_merge(
                $config['multiItems']['advertisements'],
                [
                    [
                        'label' => 'review-operating-centre-advertisement-newspaper',
                        'value' => $data['adPlacedIn']
                    ],
                    [
                        'label' => 'review-operating-centre-advertisement-date',
                        'value' => $this->formatDate($data['adPlacedDate'])
                    ],
                    [
                        'label' => 'review-operating-centre-advertisement-file',
                        'noEscape' => true,
                        'value' => $this->formatAdDocumentList($data)
                    ]
                ]
            );
        }

        return $config;
    }

    /**
     * Format ad document list
     *
     * @param array $data
     * @return string
     */
    private function formatAdDocumentList($data)
    {
        $files = [];

        foreach ($data['operatingCentre']['adDocuments'] as $document) {
            if ($document['application']['id'] == $data['application']['id']) {
                $files[] = $document['description'];
            }
        }

        if (empty($files)) {
            return $this->translate('no-files-uploaded');
        }

        return implode('<br>', $files);
    }
}
