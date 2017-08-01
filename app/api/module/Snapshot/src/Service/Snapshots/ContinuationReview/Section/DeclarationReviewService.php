<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\ApplicationUndertakingsReviewService;

/**
 * Declaration Continuation Review Service
 */
class DeclarationReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param ContinuationDetail $continuationDetail continuation detail
     *
     * @return array
     */
    public function getConfigFromData(ContinuationDetail $continuationDetail)
    {

        $items[] = [
            'label' => 'continuations.declaration.signature-type.label',
            'value' => $this->translate($this->getSignatureType($continuationDetail)),
        ];

        if ($continuationDetail->getDigitalSignature()) {
            $items[] = [
                'label' => 'continuations.declaration.signed-by',
                'value' => $continuationDetail->getDigitalSignature()->getSignatureName()
            ];
            $items[] = [
                'label' => 'continuations.declaration.date-of-birth',
                'value' => $this->formatDate($continuationDetail->getDigitalSignature()->getDateOfBirth())
            ];
            $items[] = [
                'label' => 'continuations.declaration.signature-date',
                'value' => $this->formatDate($continuationDetail->getDigitalSignature()->getCreatedOn())
            ];
        }

        /** @var ApplicationUndertakingsReviewService $applicationReviewService */
        $applicationReviewService = $this->getServiceLocator()->get('Review\ApplicationUndertakings');

        return [
            'mainItems' => [
                [
                    'markup' => $applicationReviewService->getMarkupForLicence($continuationDetail->getLicence())
                ],
                [
                    'header' => 'continuations.declaration.signature-details',
                    'items' => $items
                ],
            ]
        ];

    }

    /**
     * Get signature type text
     *
     * @param ContinuationDetail $continuationDetail Continuation detail
     *
     * @return string
     */
    private function getSignatureType(ContinuationDetail $continuationDetail)
    {
        $signatureTypeOptions = [
            RefData::SIG_DIGITAL_SIGNATURE => 'continuations.declaration.signature-type.digital',
            RefData::SIG_PHYSICAL_SIGNATURE => 'continuations.declaration.signature-type.print',
        ];

        $signatureType = 'continuations.declaration.signature-type.unknown';
        if (isset($signatureTypeOptions[$continuationDetail->getSignatureType()->getId()])) {
            $signatureType = $signatureTypeOptions[$continuationDetail->getSignatureType()->getId()];
        }

        return $signatureType;
    }
}
