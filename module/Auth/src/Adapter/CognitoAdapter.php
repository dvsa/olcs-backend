<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Adapter;

use Dvsa\Authentication\Cognito\Client;
use Dvsa\Contracts\Auth\Exceptions\ChallengeException;
use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Dvsa\Contracts\Auth\Exceptions\InvalidTokenException;
use Laminas\Authentication\Adapter\AbstractAdapter;
use Laminas\Authentication\Result;

class CognitoAdapter extends AbstractAdapter
{
    /**
     * Authentication success.
     */
    const SUCCESS_WITH_CHALLENGE = 2;

    /**
     * @var Client
     */
    protected $client;

    /**
     * CognitoAdapter constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Result
     */
    public function authenticate(): Result
    {
        try {
            $token = $this->client->authenticate($this->getIdentity(), $this->getCredential());

            // Decode and then store the ID token in the session for efficient repeated access.
            $idTokenClaims = $this->client->decodeToken($token->getIdToken());
            $accessTokenClaims = $this->client->decodeToken($token->getToken());
            $resourceOwner = $this->client->getResourceOwner($token);

            $userObject = [
                'Provider' => Client::class,
                'Token' => $token,
                'ResourceOwner' => $resourceOwner,
                'AccessToken' => $token->getToken(),
                'AccessTokenClaims' => $accessTokenClaims,
                'IdToken' => $token->getIdToken(),
                'IdTokenClaims' => $idTokenClaims,
                'RefreshToken' => $token->getRefreshToken(),
            ];

            return new Result(Result::SUCCESS, $userObject);
        } catch (ChallengeException $e) {
            return new Result(
                static::SUCCESS_WITH_CHALLENGE,
                [],
                [
                    'challengeName' => $e->getChallengeName(),
                    'challengeParameters' => $e->getParameters(),
                    'challengeSession' => $e->getSession()
                ]
            );
        } catch (InvalidTokenException | ClientException $e) {
            return new Result(Result::FAILURE, [], [$e->getMessage()]);
        }
    }
}
