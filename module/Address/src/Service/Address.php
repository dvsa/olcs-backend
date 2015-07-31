<?php

/**
 * Address
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Address\Service;

use Dvsa\Olcs\Api\Domain\Repository\AdminAreaTrafficArea;
use Dvsa\Olcs\Api\Domain\Repository\PostcodeEnforcementArea;
use Dvsa\Olcs\Api\Entity\TrafficArea\AdminAreaTrafficArea as AdminAreaTrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\EnforcementArea\PostcodeEnforcementArea as PostcodeEnforcementAreaEntity;

/**
 * Address
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Address implements AddressInterface
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function fetchTrafficAreaByPostcode($postcode, AdminAreaTrafficArea $repo)
    {
        $adminArea = $this->fetchAdminAreaByPostcode($postcode);

        if ($adminArea) {

            /** @var AdminAreaTrafficAreaEntity $record */
            $record = $repo->fetchById($adminArea);

            return $record->getTrafficArea();
        }

        return null;
    }

    public function fetchEnforcementAreaByPostcode($postcode, PostcodeEnforcementArea $repo)
    {
        preg_match('/^([^\s]+)\s(\d).+$/', $postcode, $matches);

        if (empty($matches)) {
            return null;
        }

        $prefix = $matches[1];
        $suffixDigit = $matches[2];

        // first try lookup by prefix + first digit of suffix
        /** @var PostcodeEnforcementAreaEntity $pea */
        $pea = $repo->fetchByPostcodeId($prefix . ' ' . $suffixDigit);

        if ($pea === null) {
            // if not found, try by just the prefix
            $pea = $repo->fetchByPostcodeId($prefix);
        }

        if ($pea === null) {
            return null;
        }

        return $pea->getEnforcementArea();
    }

    public function fetchAdminAreaByPostcode($postcode)
    {
        $data = $this->fetchByPostcode($postcode);

        if (!$data) {
            return null;
        }

        // yes, 'administritive_area' really is mis-spelled in API response :(
        // @NOTE not sure if we still need the elseif
        if (isset($data[0]['administritive_area'])) {
            return $data[0]['administritive_area'];
        } elseif (isset($data['administritive_area'])) {
            return $data['administritive_area'];
        }

        return null;
    }

    public function fetchByPostcode($postcode)
    {
        $this->client->setUri('address/' . urlencode($postcode));
        $response = $this->client->send();

        if ($response->isOk()) {
            $content = $response->getBody();

            return json_decode($content, true);
        }

        return false;
    }
}
