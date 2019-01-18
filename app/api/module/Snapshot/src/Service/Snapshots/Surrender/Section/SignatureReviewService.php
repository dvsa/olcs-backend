<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section;

use Dvsa\Olcs\Api\Entity\Surrender;

class SignatureReviewService extends AbstractReviewService
{

    /**
     * Format the readonly config from the given record
     *
     * @param Surrender $surrender
     * @return mixed
     */
    public function getConfigFromData(Surrender $surrender)
    {

        $signature = $surrender->getDigitalSignature();
        return [
            'markup' => $this->translateReplace(
                'markup-licence-surrender-signature-digital',
                [
                    $signature->getSignatureName(),
                    $signature->getDateOfBirth(),
                    $this->formatDate($signature->getCreatedOn(true))
                ]
            )
        ];
    }
}
