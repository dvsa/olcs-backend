<?php

/**
 * User
 */
namespace Dvsa\Olcs\Api\Service\OpenAm;

use RandomLib\Generator;

/**
 * User
 */
class User implements UserInterface
{
    /**
     * @var Client
     */
    private $openAmClient;
    /**
     * @var Generator
     */
    private $randomGenerator;

    /**
     * @param ClientInterface $openAmClient
     * @param Generator $randomGenerator
     */
    public function __construct(ClientInterface $openAmClient, Generator $randomGenerator)
    {
        $this->openAmClient = $openAmClient;
        $this->randomGenerator = $randomGenerator;
    }

    /**
     * Generates a pid
     *
     * @param string $loginId
     *
     * @return string
     */
    public function generatePid($loginId)
    {
        return hash('sha256', $loginId);
    }

    /**
     * Registers a user
     *
     * @param string $loginId
     * @param string $emailAddress
     * @param string $realm
     * @param callable $callback
     *
     * @return void
     * @throws FailedRequestException
     */
    public function registerUser($loginId, $emailAddress, $realm, $callback = null)
    {
        $pid = $this->generatePid($loginId);

        $password = $this->generatePassword();

        $this->openAmClient->registerUser(
            $loginId,
            $pid,
            $emailAddress,
            $loginId,
            $loginId,
            $realm,
            $password
        );

        if ($callback !== null) {
            $params = [
                'password' => $password
            ];

            $this->callCallbackIfExists($callback, $params);
        }
    }

    /**
     * Updates a user
     *
     * @param string $pid
     * @param string $username
     * @param string $emailAddress
     * @param bool $disabled
     *
     * @return void
     * @throws FailedRequestException
     */
    public function updateUser($pid, $username = null, $emailAddress = null, $disabled = null)
    {
        $payload = [];

        if ($username !== null) {
            $payload[] = [
                'operation' => 'replace',
                'field' => 'userName',
                'value' => $username
            ];
        }

        if ($emailAddress !== null) {
            $payload[] = [
                'operation' => 'replace',
                'field' => 'emailAddress',
                'value' => $emailAddress
            ];
        }

        if ($disabled !== null) {
            $payload[] = [
                'operation' => 'replace',
                'field' => 'inActive',
                'value' => $disabled
            ];
        }

        if (count($payload) === 0) {
            return;
        }

        $this->openAmClient->updateUser($pid, $payload);
    }

    /**
     * Disables a user
     *
     * @param string $pid
     *
     * @return void
     * @throws FailedRequestException
     */
    public function disableUser($pid)
    {
        $payload[] = [
            'operation' => 'replace',
            'field' => 'inActive',
            'value' => true
        ];

        $this->openAmClient->updateUser($pid, $payload);
    }

    /**
     * Generates a password
     *
     * @return string
     */
    private function generatePassword()
    {
        return $this->randomGenerator->generateString(12, Generator::EASY_TO_READ);
    }

    /**
     * Calls the callback function/method if exists.
     *
     * @param callable $callback
     * @param array $params
     * @throws \Exception
     */
    private function callCallbackIfExists($callback, $params)
    {
        if (is_callable($callback)) {
            $callback($params);
        } elseif (!empty($callback)) {
            throw new \Exception('Invalid callback: ' . $callback);
        }
    }
}
