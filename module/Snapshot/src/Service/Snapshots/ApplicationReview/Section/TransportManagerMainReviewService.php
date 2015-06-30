<?php

/**
 * Transport Manager Main Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Dvsa\Olcs\Api\Entity\System\Category;

/**
 * Transport Manager Main Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerMainReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = [])
    {
        $workContactDetails = $data['transportManager']['workCd'];
        $contactDetails = $data['transportManager']['homeCd'];
        $person = $contactDetails['person'];

        return [
            'multiItems' => [
                [
                    [
                        'label' => 'tm-review-main-name',
                        'value' => $this->formatPersonFullName($person)
                    ],
                    [
                        'label' => 'tm-review-main-birthDate',
                        'value' => $this->formatDate($person['birthDate'], 'd/m/Y')
                    ],
                    [
                        'label' => 'tm-review-main-birthPlace',
                        'value' => $person['birthPlace']
                    ],
                    [
                        'label' => 'tm-review-main-email',
                        'value' => $contactDetails['emailAddress']
                    ],
                    [
                        'label' => 'tm-review-main-certificate',
                        'noEscape' => true,
                        'value' => $this->formatCertificateFiles($data)
                    ],
                    [
                        'label' => 'tm-review-main-home-address',
                        'value' => $this->formatFullAddress($contactDetails['address'])
                    ],
                    [
                        'label' => 'tm-review-main-work-address',
                        'value' => $this->formatFullAddress($workContactDetails['address'])
                    ]
                ]
            ]
        ];
    }

    private function formatCertificateFiles($data)
    {
        $files = $this->findFiles(
            $data['transportManager']['documents'],
            Category::CATEGORY_TRANSPORT_MANAGER,
            Category::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION
        );

        if (empty($files)) {
            return $this->translate('tm-review-main-no-files');
        }

        $fileNames = [];

        foreach ($files as $file) {
            $fileNames[] = $file['filename'];
        }

        return implode('<br>', $fileNames);
    }
}
