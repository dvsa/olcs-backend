<?php

namespace Dvsa\Olcs\DvsaAddressService\Service;

interface AddressInterface
{
    public function lookupAddress(string $query): array;
}
