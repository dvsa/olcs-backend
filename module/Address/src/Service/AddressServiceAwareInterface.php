<?php

/**
 * Address Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\Olcs\Address\Service;

/**
 * Address Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface AddressServiceAwareInterface
{
    public function setAddressService(AddressInterface $service);

    public function getAddressService();
}
