<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Adapter;

use Dvsa\Authentication\Cognito\Client;
use Dvsa\Contracts\Auth\Exceptions\ChallengeException;
use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Dvsa\Contracts\Auth\Exceptions\InvalidTokenException;
use Dvsa\Olcs\Auth\Exception\ChangePasswordException;
use Laminas\Authentication\Adapter\AbstractAdapter;
use Laminas\Authentication\Result;
use Laminas\Http\Response;
use Olcs\Logging\Log\Logger;

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
     * @var String
     */
    protected $realm;

    /**
     * CognitoAdapter constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $realm
     */
    public function setRealm(string $realm)
    {
        $this->realm = $realm;
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

    /**
     * @todo the client doesn't currently check the old password - needs addressing in upstream DVSA client
     * @todo are there other possible exceptions we're expecting here? Upstream DVSA client only throws ClientExcpetion
     * have put in a catch-all for \Exception for now
     *
     * @param string $identifier
     * @param string $oldPassword
     * @param string $newPassword
     *
     * @return array
     * @throws ChangePasswordException
     */
    public function changePassword(string $identifier, string $oldPassword, string $newPassword): array
    {
        try {
            $success = $this->client->changePassword($identifier, $newPassword);
            $code = Response::STATUS_CODE_200;

            if (!$success) {
                $code = Response::STATUS_CODE_500;
            }

            return ['status' => $code];
        } catch (ClientException $e) {
            Logger::debug('Cognito client: change password ClientException: ' . $e->getMessage());
            throw new ChangePasswordException($e->getMessage());
        } catch (\Exception $e) {
            Logger::err('Unknown change password error from Cognito client: ' . $e->getMessage());
            throw new ChangePasswordException($e->getMessage());
        }
    }
}
