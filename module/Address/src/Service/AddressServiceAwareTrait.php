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
trait AddressServiceAwareTrait
{
    /**
     * @var AddressInterface
     */
    protected $addressService;

    public function setAddressService(AddressInterface $service)
    {
        $this->addressService = $service;
    }

    public function getAddressService()
    {
        return $this->addressService;
    }
}
