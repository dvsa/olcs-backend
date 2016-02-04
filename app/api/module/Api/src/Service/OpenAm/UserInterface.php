<?php

/**
 * User Interface
 */
namespace Dvsa\Olcs\Api\Service\OpenAm;

/**
 * User Interface
 */
interface UserInterface
{
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
    public function registerUser($loginId, $emailAddress, $realm, $callback = null);

    /**
     * Generate a Pid for a user
     *
     * @param string $loginId
     *
     * @return string
     */
    public function generatePid($loginId);

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
    public function updateUser($pid, $username = null, $emailAddress = null, $disabled = null);

    /**
     * Disables a user
     *
     * @param string $pid
     *
     * @return void
     * @throws FailedRequestException
     */
    public function disableUser($pid);

    /**
     * Is active
     *
     * @param string $pid
     *
     * @return bool
     * @throws FailedRequestException
     * @throws \RuntimeException
     */
    public function isActiveUser($pid);
}
