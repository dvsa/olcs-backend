<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Dvsa\Olcs\Api\Entity\DigitalSignature;
use Dvsa\Olcs\Api\Entity\System\RefData;

class SignatureReviewService extends AbstractReviewService
{

    /**
     * Format the readonly config from the given record
     *
     * @param array $data
     * @return mixed
     */
    public function getConfigFromData(array $data = array())
    {
        /** @var RefData $signatureType */
        $signatureType = $data['signatureType'];

        if ($signatureType->getId() === RefData::SIG_PHYSICAL_SIGNATURE) {
            return [
                'markup' => $this->translateReplace(
                    'markup-signature-physical',
                    [
                        $this->translate('directors-signature'),
                        $this->translate('return-address')
                    ]
                )
            ];
        }

        /** @var DigitalSignature $signature */
        $signature = $data['digitalSignature'];
        return [
            'markup' => $this->translateReplace(
                'markup-signature-digital',
                [
                    $signature->getSignatureName(),
                    $this->formatDate($signature->getDateOfBirth()),
                    $this->formatDate($signature->getCreatedOn(true))
                ]
            )
        ];
    }
}
