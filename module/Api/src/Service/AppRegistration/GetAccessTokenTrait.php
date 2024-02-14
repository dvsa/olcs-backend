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

        $client_id = $this->getClientId();
        $client_secret = $this->getClientSecret();
        $scope = $this->getScope();
        $proxy =  $this->getProxy();
        error_log('client_id:'. $client_id);
        error_log('secret:'. $client_secret);
        error_log('scope:'. $scope);
        error_log('proxy:'. $proxy);
        $response = $client->request(
            'POST',
            $this->getTokenUrl(),
            [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'scope' => $scope,
                ],
                'proxy' => $proxy,
            ]
        );
        $responseBody = json_decode($response->getBody()->getContents(), true);

        return $responseBody['access_token'];
    }
}
