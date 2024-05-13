<?php

namespace Dvsa\Olcs\Address\Service;

use Dvsa\Olcs\Api\Domain\Repository\AdminAreaTrafficArea;
use Dvsa\Olcs\Api\Domain\Repository\PostcodeEnforcementArea;
use Dvsa\Olcs\Api\Entity\EnforcementArea\PostcodeEnforcementArea as PostcodeEnforcementAreaEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\AdminAreaTrafficArea as AdminAreaTrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Api\Service\Exception;

/**
 * Address
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Address implements AddressInterface
{
    public const ERR_INVALID_RESP_BY_POSTCODE = 'PostCode API not response or Address was not found by postcode';
    public const ERR_INVALID_RESP_BY_UPRN = 'PostCode API not response or Address was not found by uprn';

    private $taCache = [];

    /**
     * Constructor
     *
     * @param Client $client Postcode Api Http Client
     */
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * Get traffic area by Postcode
     *
     * @param string               $postcode Post code
     * @param AdminAreaTrafficArea $repo     Repository
     *
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

    /**
     * Get enforcement area by post code
     *
     * @param string                  $postcode Postcode
     * @param PostcodeEnforcementArea $repo     Repository
     *
     * @return \Dvsa\Olcs\Api\Entity\EnforcementArea\EnforcementArea|null
     */
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

    /**
     * Get admin aread by post code
     *
     * @param string $postcode Post code
     *
     * @return string|null
     */
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

    /**
     * Request from Postcode API address details by Uprn
     *
     * @param string $postcode Post code
     *
     * @return string
     */
    public function fetchByPostcode($postcode)
    {
        $this->client->setUri('address/' . urlencode($postcode));
        $response = $this->client->send();

        if ($response->isOk()) {
            $content = $response->getBody();

            return json_decode($content, true);
        }

        throw new Exception(self::ERR_INVALID_RESP_BY_POSTCODE);
    }

    /**
     * Request from Postcode API address details by Uprn
     *
     * @param string $uprn Unique code for address in postcode Api
     *
     * @return string
     */
    public function fetchByUprn($uprn)
    {
        $this->client->setUri('address/');
        $this->client->setParameterGet(['id' => $uprn]);
        $response = $this->client->send();

        if ($response->isOk()) {
            $content = $response->getBody();

            return json_decode($content, true);
        }

        throw new Exception(self::ERR_INVALID_RESP_BY_UPRN);
    }
}
