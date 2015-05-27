<?php

namespace Dvsa\Olcs\Api\Assertion\Licence;

use ZfcRbac\Assertion\AssertionInterface;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Update Licence Type
 */
class UpdateLicenceType implements AssertionInterface
{
    public function assert(AuthorizationService $authorizationService, Licence $context = null)
    {
        // If internal then we are good to go
        if ($authorizationService->isGranted()) {
            return true;
        }

        if ($context->getGoodsOrPsv()->getId() === Licence::LICENCE_CATEGORY_GOODS_VEHICLE) {
            return true;
        }

        $allowedLicTypes = [Licence::LICENCE_TYPE_STANDARD_NATIONAL, Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL];

        if (in_array($context->getLicenceType()->getId(), $allowedLicTypes)) {
            return true;
        }

        return false;
    }
}
