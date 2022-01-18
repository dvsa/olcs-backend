<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Adapter;

use Dvsa\Authentication\Cognito\Client;
use Dvsa\Contracts\Auth\AccessTokenInterface;
use Dvsa\Contracts\Auth\Exceptions\ChallengeException;
use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Dvsa\Contracts\Auth\Exceptions\InvalidTokenException;
use Dvsa\Olcs\Auth\Exception\ChangePasswordException;
use Dvsa\Olcs\Auth\Exception\ResetPasswordException;
use Dvsa\Olcs\Transfer\Result\Auth\ChangeExpiredPasswordResult;
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

    public const AWS_ERROR_INVALID_PASSWORD = 'InvalidPasswordException';
    public const AWS_ERROR_NOT_AUTHORIZED = 'NotAuthorizedException';

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
            return new Result(Result::SUCCESS, $this->buildUserObject($token));
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

    /**
     * @todo Using change password as no separate reset password method in DVSA client (use AWS adminSetUserPassword?)
     * @todo This will only work up until change password implements checking the previous password
     *
     * @param string $identifier
     * @param string $newPassword
     *
     * @return bool
     * @throws ResetPasswordException
     */
    public function resetPassword(string $identifier, string $newPassword): bool
    {
        try {
            return $this->client->changePassword($identifier, $newPassword);
        } catch (ClientException $e) {
            Logger::debug('Cognito client: reset password ClientException: ' . $e->getMessage());
            throw new ResetPasswordException($e->getMessage());
        } catch (\Exception $e) {
            Logger::err('Unknown reset password error from Cognito client: ' . $e->getMessage());
            throw new ResetPasswordException($e->getMessage());
        }
    }

    /**
     * @param string $identifier
     * @param string $password
     * @param string $email
     * @param array|null $attributes
     * @throws ClientException
     */
    public function register(string $identifier, string $password, string $email, array $attributes = []): void
    {
        $attributes = array_merge(['email' => $email], $attributes);
        $this->client->register($identifier, $password, $attributes);
    }

    /**
     * @param string $newPassword
     * @param string $challengeToken
     * @return Result
     */
    public function changeExpiredPassword(string $newPassword, string $challengeToken, string $username): ChangeExpiredPasswordResult
    {
        try {
            $token = $this->client->responseToAuthChallenge(
                'NEW_PASSWORD_REQUIRED',
                [
                    'NEW_PASSWORD' => $newPassword,
                    'USERNAME' => $username
                ],
                $challengeToken
            );

            return new ChangeExpiredPasswordResult(ChangeExpiredPasswordResult::SUCCESS, $this->buildUserObject($token));
        } catch (ChallengeException $e) {
            return new ChangeExpiredPasswordResult(
                ChangeExpiredPasswordResult::SUCCESS_WITH_CHALLENGE,
                [],
                [],
                [
                    'challengeName' => $e->getChallengeName(),
                    'challengeParameters' => $e->getParameters(),
                    'challengeSession' => $e->getSession()
                ]
            );
        } catch (ClientException $e) {
            switch ($e->getPrevious()->getAwsErrorCode()) {
                case 'InvalidPasswordException':
                    return new ChangeExpiredPasswordResult(ChangeExpiredPasswordResult::FAILURE_NEW_PASSWORD_INVALID, [], [$e->getMessage()]);
                case 'NotAuthorizedException':
                    return new ChangeExpiredPasswordResult(ChangeExpiredPasswordResult::FAILURE_NOT_AUTHORIZED, [], [$e->getMessage()]);
                default:
                    return new ChangeExpiredPasswordResult(ChangeExpiredPasswordResult::FAILURE, [], [$e->getMessage()]);
            }
        } catch (InvalidTokenException $e) {
            return new ChangeExpiredPasswordResult(ChangeExpiredPasswordResult::FAILURE, [], [$e->getMessage()]);
        }
    }

    /**
     * @param string $refreshToken
     * @param string $identifier
     * @return Result
     */
    public function refreshToken(string $refreshToken, string $identifier): Result
    {
        try {
            $token = $this->client->refreshTokens($refreshToken, $identifier);
            return new Result(Result::SUCCESS, $this->buildUserObject($token));
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
     * @param AccessTokenInterface $token
     * @return array
     * @throws InvalidTokenException
     */
    private function buildUserObject(AccessTokenInterface $token): array
    {
        $idTokenClaims = $this->client->decodeToken($token->getIdToken());
        $accessTokenClaims = $this->client->decodeToken($token->getToken());
        $resourceOwner = $this->client->getResourceOwner($token);

        return [
            'Provider' => Client::class,
            'Token' => $token,
            'ResourceOwner' => $resourceOwner,
            'AccessToken' => $token->getToken(),
            'AccessTokenClaims' => $accessTokenClaims,
            'IdToken' => $token->getIdToken(),
            'IdTokenClaims' => $idTokenClaims,
            'RefreshToken' => $token->getRefreshToken(),
        ];
    }
}
