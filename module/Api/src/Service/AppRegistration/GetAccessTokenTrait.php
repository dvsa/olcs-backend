<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Service\AppRegistration;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Client\ClientInterface;

trait GetAccessTokenTrait
{
    /**
     * @param ClientInterface $client
     * @return string
     */
    public function getAccessToken(ClientInterface $client): string
    {
        $response = $client->request(
            'POST',
            $this->getTokenUrl(),
            [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->getClientId(),
                    'client_secret' => $this->getClientSecret(),
                    'scope' => $this->getScope(),
                ],
                'proxy' => $this->getProxy(),
            ]
        );
        $responseBody = json_decode($response->getBody()->getContents(), true);

        return $responseBody['access_token'];
    }
}
