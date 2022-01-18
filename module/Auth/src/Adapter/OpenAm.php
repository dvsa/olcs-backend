<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Adapter;

use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Dvsa\Olcs\Auth\Client\OpenAm as OpenAmClient;
use Dvsa\Olcs\Auth\Exception\ChangePasswordException;
use Laminas\Authentication\Adapter\AbstractAdapter;
use Laminas\Authentication\Result as AuthResult;
use Olcs\Logging\Log\Logger;

class OpenAm extends AbstractAdapter
{
    const OPEN_AM_EXCEPTION = 'OpenAm returned exception';
    const SUCCESS_WITH_CHALLENGE = 2;
    const CHALLENGE_NEW_PASSWORD_REQUIRED = 'NEW_PASSWORD_REQUIRED';

    /**
     * @var OpenAmClient
     */
    private $client;

    /**
     * @var string
     */
    private $realm;

    private PidIdentityProvider $identityProvider;

    /**
     * @param OpenAmClient $client
     *
     * @return void
     */
    public function __construct(OpenAmClient $client, PidIdentityProvider $identityProvider)
    {
        $this->client = $client;
        $this->identityProvider = $identityProvider;
    }

    /**
     * @param string $realm
     */
    public function setRealm(string $realm)
    {
        $this->realm = $realm;
    }

    /**
     * @return AuthResult
     */
    public function authenticate(): AuthResult
    {
        try {
            $result = $this->client->authenticate($this->getIdentity(), $this->getCredential(), $this->realm);

            return $this->handleAuthenticateResult($result);
        } catch (\Exception $e) {
            Logger::err(self::OPEN_AM_EXCEPTION, [$e->getMessage()]);
            return new AuthResult(AuthResult::FAILURE_UNCATEGORIZED, [], [$e->getMessage()]);
        }
    }

    /**
     * Forgot password (generates email to user)
     *
     * @param string $username
     * @param string $subject
     * @param string $message
     *
     * @return array
     */
    public function forgotPassword(string $username, string $subject, string $message): array
    {
        return $this->client->forgotPassword($username, $subject, $message, $this->realm);
    }

    /**
     * Confirm link is still valid (based on original from olcs-auth repo)
     *
     * @param string $username       Username
     * @param string $confirmationId Confirmation id
     * @param string $tokenId        Token id
     *
     * @return array
     */
    public function confirmPasswordResetValid(string $username, string $confirmationId, string $tokenId): array
    {
        return $this->client->confirmPasswordResetValid($username, $confirmationId, $tokenId, $this->realm);
    }

    /**
     * Reset password (based on original from olcs-auth repo)
     *
     * @param string $username       Username
     * @param string $confirmationId Confirmation id
     * @param string $tokenId        Token id
     * @param string $newPassword    New password
     *
     * @return array
     */
    public function resetPassword(string $username, string $confirmationId, string $tokenId, string $newPassword): array
    {
        return $this->client->resetPassword($username, $newPassword, $confirmationId, $tokenId, $this->realm);
    }

    /**
     * Update password (based on original from olcs-auth repo)
     *
     * @param string $username    Username
     * @param string $oldPassword Old password
     * @param string $newPassword New password
     *
     * @return array
     * @throws ChangePasswordException
     */
    public function changePassword(string $username, string $oldPassword, string $newPassword): array
    {
        $token = $this->identityProvider->getToken();

        try {
            return $this->client->changePassword($username, $oldPassword, $newPassword, $this->realm, $token);
        } catch (\Exception $e) {
            throw new ChangePasswordException($e->getMessage());
        }
    }

    /**
     * @param $status
     * @return int
     */
    private function getAuthenticationCode(array $result): int
    {
        switch ($result['status']) {
            case 200:
                $authResult = isset($result['tokenId']) ? AuthResult::SUCCESS : static::SUCCESS_WITH_CHALLENGE;
                break;
            case 401:
                $authResult = AuthResult::FAILURE;
                break;
            default:
                $authResult = AuthResult::FAILURE_UNCATEGORIZED;
                break;
        }
        return $authResult;
    }

    /**
     * @param array $result
     * @return AuthResult
     */
    private function handleAuthenticateResult(array $result): AuthResult
    {
        switch ($authCode = $this->getAuthenticationCode($result)) {
            case AuthResult::SUCCESS:
                return new AuthResult($authCode, $result);
            case AuthResult::FAILURE:
                return new AuthResult($authCode, [], [$result['message']]);
            case static::SUCCESS_WITH_CHALLENGE:
                return new AuthResult(
                    $authCode,
                    [],
                    [
                        'challengeName' => static::CHALLENGE_NEW_PASSWORD_REQUIRED,
                        'challengeParameters' => [
                            'authId' => $result['authId']
                        ]
                    ]
                );
            default:
                return new AuthResult($authCode, []);
        }
    }
}
