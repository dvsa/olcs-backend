<?php

/**
 * Transport Manager Main Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section;

use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Api\Entity\Doc\Document;

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
    public function getConfig(TransportManagerApplication $tma)
    {
        $tm = $tma->getTransportManager();

        $workContactDetails = $tm->getWorkCd();
        $contactDetails = $tm->getHomeCd();

        $person = $contactDetails->getPerson();

        return [
            'multiItems' => [
                [
                    [
                        'label' => 'tm-review-main-name',
                        'value' => $this->formatPersonFullName($person)
                    ],
                    [
                        'label' => 'tm-review-main-birthDate',
                        'value' => $this->formatDate($person->getBirthDate(), 'd/m/Y')
                    ],
                    [
                        'label' => 'tm-review-main-birthPlace',
                        'value' => $person->getBirthPlace()
                    ],
                    [
                        'label' => 'tm-review-main-email',
                        'value' => $contactDetails->getEmailAddress()
                    ],
                    [
                        'label' => 'tm-review-main-certificate',
                        'noEscape' => true,
                        'value' => $this->formatCertificateFiles($tma)
                    ],
                    [
                        'label' => 'tm-review-main-home-address',
                        'value' => $this->formatFullAddress($contactDetails->getAddress())
                    ],
                    [
                        'label' => 'tm-review-main-work-address',
                        'value' => $this->formatFullAddress($workContactDetails->getAddress())
                    ]
                ]
            ]
        ];
    }

    private function formatCertificateFiles(TransportManagerApplication $tma)
    {
        $files = $this->findFiles(
            $tma->getTransportManager()->getDocuments(),
            Category::CATEGORY_TRANSPORT_MANAGER,
            Category::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION
        );

        if ($files->isEmpty()) {
            return $this->translate('tm-review-main-no-files');
        }

        $fileNames = [];

        /** @var Document $file */
        foreach ($files as $file) {
            $fileNames[] = $file->getFilename();
        }

        return implode('<br>', $fileNames);
    }
}
