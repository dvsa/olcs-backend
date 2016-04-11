<?php

/**
 * Application Financial Evidence Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Dvsa\Olcs\Transfer\Query\Application\FinancialEvidence;

/**
 * Application Financial Evidence Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationFinancialEvidenceReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        // @NOTE Tmp solution during migration
        $feData = $this->getServiceLocator()->get('QueryHandlerManager')
            ->handleQuery(FinancialEvidence::create(['id' => $data['id']]))
            ->serialize();

        $financialEvidenceData = $feData['financialEvidence'];

        return [
            'multiItems' => [
                [
                    [
                        'label' => 'application-review-financial-evidence-no-of-vehicles',
                        'value' => $this->getTotalVehicles($financialEvidenceData)
                    ],
                    [
                        'label' => 'application-review-financial-evidence-required-finance',
                        'value' => $this->formatAmount($financialEvidenceData['requiredFinance'])
                    ],
                    [
                        'label' => 'application-review-financial-evidence-evidence',
                        'noEscape' => true,
                        'value' => $this->getEvidence($data, $feData)
                    ]
                ]
            ]
        ];
    }

    private function getTotalVehicles($financialEvidenceDate)
    {
        return (isset($financialEvidenceDate['applicationVehicles']) ?
            (int)$financialEvidenceDate['applicationVehicles'] : 0) +
            (isset($financialEvidenceDate['otherLicenceVehicles']) ?
            (int)$financialEvidenceDate['otherLicenceVehicles'] : 0) +
            (isset($financialEvidenceDate['otherApplicationVehicles']) ?
            (int)$financialEvidenceDate['otherApplicationVehicles'] : 0);
    }

    private function getEvidence($data, $feData)
    {
        if ($data['financialEvidenceUploaded'] === 'N') {
            return $this->translate('application-review-financial-evidence-evidence-post');
        }

        $documents = is_array($feData['documents']) ? $feData['documents'] : [];

        return $this->formatDocumentList($documents);
    }

    /**
     * Format document list
     *
     * @param array $documents
     * @return string
     */
    private function formatDocumentList($documents)
    {
        $files = [];

        foreach ($documents as $document) {
            $files[] = $document->getDescription();
        }

        return implode('<br>', $files);
    }
}
