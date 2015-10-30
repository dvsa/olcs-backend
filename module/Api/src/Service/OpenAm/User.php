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
     * @var string
     */
    private $reservedPid;

    /**
     * @param ClientInterface $openAmClient
     * @param Generator $randomGenerator
     */
    public function __construct(ClientInterface $openAmClient, Generator $randomGenerator)
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
     * @param $realm
     * @return void
     * @throws FailedRequestException
     *
     * @TODO add email sending.
     */
    public function registerUser($loginId, $emailAddress, $realm)
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
            $loginId,
            $loginId,
            $realm,
            $this->generatePassword()
        );
    }

    public function updateUser($username, $emailAddress = null, $enabled = null)
    {
        $payload = [];

        if ($emailAddress !== null) {
            $payload[] = [
                'operation' => 'replace',
                'field' => 'emailAddress',
                'value' => $emailAddress
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
