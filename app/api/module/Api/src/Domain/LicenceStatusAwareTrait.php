<?php

namespace Dvsa\Olcs\Api\Domain;


use Dvsa\Olcs\Api\Entity\Licence\Licence;

trait LicenceStatusAwareTrait
{

    private function isLicenceStatusSurrenderable(Licence $licence): bool
    {
        return $this->isLicenceStatusAccessibleForExternalUser($licence);
    }

    private function isLicenceStatusAccessibleForExternalUser(Licence $licence): bool
    {
        $statusesAccessibleForExternalUser = [
            Licence::LICENCE_STATUS_VALID,
            Licence::LICENCE_STATUS_SUSPENDED,
            Licence::LICENCE_STATUS_CURTAILED,
        ];

        return in_array($licence->getStatus()->getId(), $statusesAccessibleForExternalUser);
    }

}