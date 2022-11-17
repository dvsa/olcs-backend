<?php

namespace Dvsa\Olcs\Api\Service\GovUkAccount;

use Dvsa\GovUkAccount\Exception\InvalidTokenException;
use Dvsa\GovUkAccount\Provider\GovUkAccount;
use Dvsa\GovUkAccount\Provider\GovUkAccountUser;
use Dvsa\GovUkAccount\Token\AccessToken;
use Dvsa\Olcs\Api\Service\GovUkAccount\Response\GetAuthorisationUrlResponse;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class GovUkAccountService
{
    const JWT_TIMESTAMP_LEEWAY_SECONDS = 30;

    protected GovUkAccount $provider;
    protected array $config;

    public function __construct(array $config, GovUkAccount $provider)
    {
        JWT::$leeway = static::JWT_TIMESTAMP_LEEWAY_SECONDS;
        $this->config = $config;
        $this->provider = $provider;
    }

    public function getAuthorisationUrl(string $state, bool $identityAssurance = false, array $scopes = [], string $nonce = null): GetAuthorisationUrlResponse
    {
        if (empty($scopes)) {
            $scopes = $this->provider::DEFAULT_SCOPES;
        }

        $authorizationUrlParams = [
            'scope' => $scopes,
            'redirect_uri' => $this->config['redirect_uri']['logged_in'],
        ];

        $this->provider->setState($state);
        $this->provider->setNonce($nonce);

        if ($identityAssurance) {
            $authorizationUrlParams['vtr'] = '["P2.Cl.Cm"]';
            $authorizationUrlParams['claims'] = json_encode([
                "userinfo" => [
                    GovUkAccountUser::KEY_CLAIMS_CORE_IDENTITY => null,
                ]
            ]);
        }

        return new GetAuthorisationUrlResponse(
            $this->provider->getAuthorizationUrl($authorizationUrlParams),
            $this->provider->getState(),
            $this->provider->getNonce()
        );
    }

    /**
     * Creates a JWT with claims from $data and signs it. For use with the $state param on getAuthorisationUrl() which
     * is replayed when user returns the service.
     *
     * @param array $data
     * @return string
     */
    public function createStateToken(array $data): string
    {
        return JWT::encode($data, base64_decode($this->config['keys']['private_key']), $this->config['keys']['algorithm']);
    }

    /**
     * Parses and validates the state JWT and returns the claims if successful.
     *
     * @param string $token
     * @return array
     */
    public function getStateClaimsFromToken(string $token): array
    {
        return (array) JWT::decode($token, new Key(base64_decode($this->config['keys']['public_key']), $this->config['keys']['algorithm']));
    }

    /**
     * @throws IdentityProviderException
     */
    public function getAccessToken(string $code, array $scopes = [], string $grant = 'authorization_code'): AccessToken
    {
        if (empty($scopes)) {
            $scopes = $this->provider::DEFAULT_SCOPES;
        }

        return $this->provider->getAccessToken($grant, [
            'scope' => implode($this->provider::SCOPE_SEPARATOR, $scopes),
            'code' => $code
        ]);
    }

    public function getUserDetails(AccessToken $token): \League\OAuth2\Client\Provider\ResourceOwnerInterface
    {
        return $this->provider->getResourceOwner($token);
    }

    /**
     * @throws InvalidTokenException
     */
    public function verifyIdToken(AccessToken $token, string $nonce = null)
    {
        $this->provider->validateIdToken($token->getIdToken(), $nonce);
    }

    /**
     * Checks to see if the $actual vector of trust meets the $minimumConfidence required.
     *
     * Supports ONLY the vectors of trust supported by GOV.UK Account service.
     *
     * @see https://docs.sign-in.service.gov.uk/integrate-with-integration-environment/choose-the-level-of-identity-confidence/
     * @see https://datatracker.ietf.org/doc/html/rfc8485#appendix-A.1
     * @param string $actual
     * @param string $minimumConfidence
     * @return bool
     */
    static function meetsVectorOfTrust(string $actual, string $minimumConfidence): bool
    {
        $actual = strtoupper($actual);
        $minimumConfidence = strtoupper($minimumConfidence);
        $vectorsOfTrust = ['P0', 'P1', 'P2'];

        if (!in_array($minimumConfidence, $vectorsOfTrust)) {
            throw new \InvalidArgumentException("The minimumConfidence specified is not a valid value; supported values are: " . implode(',', $vectorsOfTrust));
        }

        if (!in_array($actual, $vectorsOfTrust)) {
            return false;
        }

        $actualIndex = array_search($actual, $vectorsOfTrust);
        $minimumConfidenceIndex = array_search($minimumConfidence, $vectorsOfTrust);

        if ($actualIndex >= $minimumConfidenceIndex) {
            return true;
        }

        return false;
    }
}
