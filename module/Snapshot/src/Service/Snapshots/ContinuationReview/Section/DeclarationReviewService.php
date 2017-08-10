<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\System\RefData;

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

        return [
            'mainItems' => [
                [
                    'markup' => $this->getDeclarationMarkup($continuationDetail)
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
        if (isset($signatureTypeOptions[(string)$continuationDetail->getSignatureType()])) {
            $signatureType = $signatureTypeOptions[$continuationDetail->getSignatureType()->getId()];
        }

        return $signatureType;
    }

    /**
     * Get the markup for the declaration content
     *
     * @param ContinuationDetail $continuationDetail Continuation detail
     *
     * @return string
     */
    public function getDeclarationMarkup(ContinuationDetail $continuationDetail)
    {
        $licence = $continuationDetail->getLicence();
        if ($licence->isGoods()) {
            // Goods
            if ($licence->isNi()) {
                $markupKey = 'markup-continuation-declaration-goods-ni';
                $markupStandard = 'markup-continuation-declaration-goods-ni-standard';
            } else {
                $markupKey = 'markup-continuation-declaration-goods-gb';
                $markupStandard = 'markup-continuation-declaration-goods-gb-standard';
            }
        } else {
            // PSV
            if ($licence->isSpecialRestricted()) {
                $markupKey = 'markup-continuation-declaration-psv-sr';
            } else {
                $markupKey = 'markup-continuation-declaration-psv';
                $markupStandard = 'markup-continuation-declaration-psv-standard';
            }
        }

        if ($licence->isStandardNational() || $licence->isStandardInternational() ) {
            // add extra bullets if licence is a stanard
            $additional = [$this->translate($markupStandard)];
        } else {
            $additional = [''];
        }
        $markup = $this->translateReplace($markupKey, $additional);

        return $markup;
    }
}
