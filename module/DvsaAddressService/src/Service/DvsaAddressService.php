<?php

namespace Dvsa\Olcs\DvsaAddressService\Service;

use Dvsa\Olcs\DvsaAddressService\Client\DvsaAddressServiceClient;
use Dvsa\Olcs\DvsaAddressService\Exception\ServiceException;
use Dvsa\Olcs\DvsaAddressService\Model\Address;
use GuzzleHttp\Exception\GuzzleException;
use Laminas\Log\LoggerInterface;

class DvsaAddressService implements AddressInterface
{
    public function __construct(protected LoggerInterface $logger, protected DvsaAddressServiceClient $dvsaAddressServiceClient)
    {
    }

    /**
     * @return Address[]
     */
    public function lookupAddress(string $query): array
    {
        try {
            return $this->dvsaAddressServiceClient->lookupAddress($query);
        } catch (ServiceException|GuzzleException $e) {
            $this->logger->err('DVSA Address Service: Error looking up address by query', [
                'query' => $query,
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
