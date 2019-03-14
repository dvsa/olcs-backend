<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Api\Entity\System\RefData;

class SignatureReviewService extends AbstractReviewService
{

    /**
     * Format the readonly config from the given record
     *
     * @param Surrender $surrender
     *
     * @return mixed
     */
    public function getConfigFromData(array $application = array())
    {
        if ($application['signatureType']['id'] === RefData::SIG_PHYSICAL_SIGNATURE) {
            return [
                'markup' => $this->translateReplace(
                    'markup-licence-surrender-signature-physical',
                    [
                        $this->translate('directors-signature'),
                        $this->translate('surrender-application-return-address')
                    ]
                )
            ];
        }

        $signature = $application['digitalSignature'];
        return [
            'markup' => $this->translateReplace(
                'markup-licence-surrender-signature-digital',
                [
                    $signature->getSignatureName(),
                    $this->formatDate($signature->getDateOfBirth()),
                    $this->formatDate($signature->getCreatedOn(true))
                ]
            )
        ];
    }
}
