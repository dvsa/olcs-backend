<?php
namespace Dvsa\Olcs\Api\Service\OpenAm;

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
    public function registerUser($loginId, $emailAddress, $firstName, $lastName, $realm);

    /**
     * Generate and reserve a Pid for a user
     *
     * @return string
     */
    public function reservePid();

    public function updateUser($username, $emailAddress = null, $commonName = null, $surName = null, $enabled = null);

    public function disableUser($username);
}
