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
     * @param $loginId
     * @param $emailAddress
     * @param $firstName
     * @param $lastName
     * @param $realm
     * @return string
     * @throws FailedRequestException
     */
    public function registerUser($loginId, $emailAddress, $realm, $callback = null);

    /**
     * Generate and reserve a Pid for a user
     *
     * @return string
     */
    public function reservePid();

    public function updateUser($username, $emailAddress = null, $enabled = null);

    public function disableUser($username);
}
