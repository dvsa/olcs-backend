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

    public function updateUser($username, $emailAddress = null, $commonName = null, $surName = null, $enabled = null)
    {
        $payload = [];

        if ($emailAddress !== null) {
            $payload[] = [
                'operation' => 'replace',
                'field' => 'emailAddress',
                'value' => $emailAddress
            ];
        }

        if ($commonName !== null) {
            $payload[] = [
                'operation' => 'replace',
                'field' => 'commonName',
                'value' => $commonName
            ];
        }

        if ($surName !== null) {
            $payload[] = [
                'operation' => 'replace',
                'field' => 'surName',
                'value' => $surName
            ];
        }

        if ($enabled !== null) {
            $payload[] = [
                'operation' => 'replace',
                'field' => 'olcsInActive',
                'value' => $enabled
            ];
        }

        if (count($payload) === 0) {
            return;
        }

        $this->openAmClient->updateUser($username, $payload);
    }

    public function disableUser($username)
    {
        $payload[] = [
            'operation' => 'replace',
            'field' => 'olcsInActive',
            'value' => true
        ];

        $this->openAmClient->updateUser($username, $payload);
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
