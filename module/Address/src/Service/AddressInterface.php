<?php

/**
 * Address Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\Olcs\Address\Service;

use Dvsa\Olcs\Api\Domain\Repository\AdminAreaTrafficArea;
use Dvsa\Olcs\Api\Domain\Repository\PostcodeEnforcementArea;

/**
 * Address Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface AddressInterface
{
    public function fetchTrafficAreaByPostcode($postcode, AdminAreaTrafficArea $repo);

    public function fetchEnforcementAreaByPostcode($postcode, PostcodeEnforcementArea $repo);

    public function fetchAdminAreaByPostcode($postcode);

    public function fetchByPostcode($postcode);
}
