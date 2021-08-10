<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Adapter;

use Dvsa\Olcs\Auth\Client\OpenAm as OpenAmClient;
use Laminas\Authentication\Adapter\AbstractAdapter;
use Laminas\Authentication\Result as AuthResult;
use Laminas\Http\Headers;
use Olcs\Logging\Log\Logger;

class OpenAm extends AbstractAdapter
{
    const OPEN_AM_EXCEPTION = 'OpenAm returned exception';

    /**
     * @var OpenAmClient
     */
    private $client;

    /**
     * @param OpenAmClient $client
     *
     * @return void
     */
    public function __construct(OpenAmClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return AuthResult
     */
    public function authenticate(): AuthResult
    {
        try {
            $result = $this->client->authenticate($this->getIdentity(), $this->getCredential());

            switch ($result['status']) {
                case 200: {
                    $authResult = AuthResult::SUCCESS;
                    break;
                }
                case 401: {
                    $authResult = AuthResult::FAILURE;
                    break;
                }
                default: {
                    $authResult = AuthResult::FAILURE_UNCATEGORIZED;
                    break;
                }
            }
        } catch (\Exception $e) {
            $result = ['message' => $e->getMessage()];
            Logger::err(self::OPEN_AM_EXCEPTION, $result);
            $authResult = AuthResult::FAILURE_UNCATEGORIZED;
        }

        return new AuthResult($authResult, $result);
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
    public function confirmPasswordLink(string $username, string $confirmationId, string $tokenId): array
    {
        $data = [
            'username' => $username,
            'tokenId' => $tokenId,
            'confirmationId' => $confirmationId
        ];

        return $this->client->makeRequest('json/users?_action=confirm', $data);
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
        $data = [
            'userpassword' => $newPassword,
            'username' => $username,
            'tokenId' => $tokenId,
            'confirmationId' => $confirmationId
        ];

        return $this->client->makeRequest('json/users?_action=forgotPasswordReset', $data);
    }

    /**
     * Update password (based on original from olcs-auth repo)
     *
     * @param string $username    Username
     * @param string $oldPassword Old password
     * @param string $newPassword New password
     * @param string $token       token
     *
     * @return array
     */
    public function updatePassword(string $username, string $oldPassword, string $newPassword, string $token): array
    {
        $data = [
            'currentpassword' => $oldPassword,
            'userpassword' => $newPassword
        ];

        $uri = sprintf('json/users/%s?_action=changePassword', $username);

        $headers = new Headers();
        $headers->addHeaderLine('secureToken', $token);

        return $this->client->makeRequest($uri, $data, $headers);
    }
}
