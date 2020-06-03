<?php

namespace Dvsa\Olcs\Api\Service\OpenAm;

use Faker\Generator;

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
     * Construct
     *
     * @param ClientInterface $openAmClient    OpenAM client
     * @param Generator       $randomGenerator Random generator
     *
     * @return void
     */
    public function __construct(ClientInterface $openAmClient, Generator $randomGenerator)
    {
        $this->openAmClient = $openAmClient;
        $this->randomGenerator = $randomGenerator;
    }

    /**
     * Generates a pid
     *
     * @param string $loginId Login id
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
     * @param string   $loginId      Login id
     * @param string   $emailAddress Email address
     * @param string   $realm        Realm
     * @param callable $callback     Callback
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
     * @param string $pid          Pid
     * @param string $username     Username
     * @param string $emailAddress Email address
     * @param bool   $disabled     Disabled
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
     * @param string $pid Pid
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
     * Resets user password
     *
     * @param string   $pid      Pid
     * @param callable $callback Callback
     *
     * @return void
     * @throws FailedRequestException
     */
    public function resetPassword($pid, $callback = null)
    {
        $password = $this->generatePassword();

        $payload[] = [
            'operation' => 'replace',
            'field' => 'password',
            'value' => $password
        ];

        $this->openAmClient->updateUser($pid, $payload);

        if ($callback !== null) {
            $params = [
                'password' => $password
            ];

            $this->callCallbackIfExists($callback, $params);
        }
    }

    /**
     * Fetch user
     *
     * @param string $pid Pid
     *
     * @return array
     * @throws FailedRequestException
     * @throws \RuntimeException
     */
    public function fetchUser($pid): array
    {
        return $this->openAmClient->fetchUser($pid);
    }

    /**
     * Fetch multiple users
     *
     * @param array $pids
     * @return array
     * @throws FailedRequestException
     */
    public function fetchUsers(array $pids): array
    {
        return $this->openAmClient->fetchUsers($pids);
    }

    /**
     * Is active
     *
     * @param string $pid Pid
     *
     * @return bool
     * @throws FailedRequestException
     * @throws \RuntimeException
     */
    public function isActiveUser($pid)
    {
        // is active if successfully logged in at least once
        return !empty($this->fetchUser($pid)['lastLoginTime']);
    }

    /**
     * Generates a password
     *
     * @return string
     */
    public function generatePassword()
    {
        // make sure that generated password contains at least one upper-case, lower-case and digit
        $firstLetter = $this->randomGenerator->randomLetter;
        $secondLetter = $this->randomGenerator->randomLetter;

        return $this->randomGenerator->toUpper($firstLetter) .
            $this->randomGenerator->toLower($secondLetter) .
            $this->randomGenerator->randomNumber(1) .
            $this->randomGenerator->regexify('[A-Za-z0-9]+\[A-Za-z]{5,7}$');
    }

    /**
     * Calls the callback function/method if exists.
     *
     * @param callable $callback Callback
     * @param array    $params   Params
     *
     * @return void
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
