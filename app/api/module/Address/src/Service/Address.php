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
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;

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

    private $taCache = [];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $postcode
     * @param AdminAreaTrafficArea $repo
     * @return TrafficArea
     * @throws \Dvsa\Olcs\Api\Domain\Exception\NotFoundException
     */
    public function fetchTrafficAreaByPostcode($postcode, AdminAreaTrafficArea $repo)
    {
        if (!array_key_exists($postcode, $this->taCache)) {

            $adminArea = $this->fetchAdminAreaByPostcode($postcode);

            if ($adminArea) {

                /** @var AdminAreaTrafficAreaEntity $record */
                $record = $repo->fetchById($adminArea);

                $this->taCache[$postcode] = $record->getTrafficArea();
            } else {
                $this->taCache[$postcode] = null;
            }
        }

        return $this->taCache[$postcode];
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

    public function fetchByUprn($uprn)
    {
        // @todo move parameters to client
        $this->client->setUri('address/?id=' . $uprn);
        $response = $this->client->send();

        if ($response->isOk()) {
            $content = $response->getBody();

            return json_decode($content, true);
        }

        return false;
    }
}
