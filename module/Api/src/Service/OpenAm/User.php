<?php

namespace Dvsa\Olcs\Api\Service\OpenAm;

use RandomLib\Generator;

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
     * @var string
     */
    private $reservedPid;

    /**
     * @param Client $openAmClient
     * @param Generator $randomGenerator
     */
    public function __construct(Client $openAmClient, Generator $randomGenerator)
    {
        $this->openAmClient = $openAmClient;
        $this->randomGenerator = $randomGenerator;
    }

    public function reservePid()
    {
        if ($this->reservedPid === null) {
            $this->reservedPid = $this->generatePid();
        }
        return $this->reservedPid;
    }

    /**
     * @param $loginId
     * @param $emailAddress
     * @param $firstName
     * @param $lastName
     * @param $realm
     * @return void
     * @throws FailedRequestException
     *
     * @TODO add email sending.
     */
    public function registerUser($loginId, $emailAddress, $firstName, $lastName, $realm)
    {
        $pid = $this->reservedPid;
        if ($pid === null) {
            $pid = $this->reservePid();
        }
        $this->reservedPid = null;

        $this->openAmClient->registerUser(
            $loginId,
            $pid,
            $emailAddress,
            $lastName,
            $firstName,
            $realm,
            $this->generatePassword()
        );
    }

    private function generatePid()
    {
        return $this->randomGenerator->generateString(32, '0123456789abcdef');
    }

    private function generatePassword()
    {
        return $this->randomGenerator->generateString(12);
    }
}
