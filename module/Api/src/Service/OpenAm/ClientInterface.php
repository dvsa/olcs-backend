<?php

namespace Dvsa\Olcs\Api\Service\OpenAm;
use Zend\Http\Request;

/**
 * Class Client
 * @package Dvsa\Olcs\Api\Service\OpenAm
 */
interface ClientInterface
{
    /**
     * @param $username
     * @param $pid
     * @param $emailAddress
     * @param $surname
     * @param $commonName
     * @param $realm
     * @param $password
     * @throws FailedRequestException
     */
    public function registerUser($username, $pid, $emailAddress, $surname, $commonName, $realm, $password);

    public function updateUser($username, $updates);
}
