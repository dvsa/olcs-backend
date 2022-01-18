<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\Command\Auth\ResetPasswordOpenAm as ResetPasswordOpenAmCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Auth\ResetPasswordOpenAm;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\Http\Response;
use Mockery as m;

/**
 * @see ResetPasswordOpenAm
 */
class ResetPasswordOpenAmTest extends CommandHandlerTestCase
{
    private m\MockInterface $adapter;
    private string $username = 'username';
    private string $password = 'password';
    private string $realm = 'realm';
    private string $confirmationId = 'confirmation';
    private string $tokenId = 'token';
    private ResetPasswordOpenAmCmd $command;

    public function setUp(): void
    {
        $this->adapter = m::mock(ValidatableAdapterInterface::class);
        $this->adapter->expects('setRealm')->with($this->realm);

        $this->command = $this->getCommand();
        $this->sut = new ResetPasswordOpenAm($this->adapter);

        parent::setUp();
    }

    public function testHandleCommandResetExpired(): void
    {
        $this->adapterCheckValid(Response::STATUS_CODE_500);
        $expectedResult = $this->expectedResult(ResetPasswordOpenAm::MSG_EXPIRED_LINK, true);
        $this->assertEquals($expectedResult, $this->sut->handleCommand($this->command)->toArray());
    }

    public function testHandleCommandResetFail(): void
    {
        $this->adapterCheckValid(Response::STATUS_CODE_200);
        $this->adapterResetAttempt(Response::STATUS_CODE_500);
        $expectedResult = $this->expectedResult(ResetPasswordOpenAm::MSG_GENERIC_FAIL);
        $this->assertEquals($expectedResult, $this->sut->handleCommand($this->command)->toArray());
    }

    public function testHandleCommandResetException(): void
    {
        $this->adapterCheckValid(Response::STATUS_CODE_200);
        $this->adapter
            ->expects('resetPassword')
            ->with($this->username, $this->confirmationId, $this->tokenId, $this->password)
            ->andThrow(new \Exception());
        $expectedResult = $this->expectedResult(ResetPasswordOpenAm::MSG_GENERIC_FAIL);
        $this->assertEquals($expectedResult, $this->sut->handleCommand($this->command)->toArray());
    }

    public function testHandleCommandResetSuccess(): void
    {
        $this->adapterCheckValid(Response::STATUS_CODE_200);
        $this->adapterResetAttempt(Response::STATUS_CODE_200);
        $expectedResult = $this->expectedResult(ResetPasswordOpenAm::MSG_GENERIC_SUCCESS, false, true);
        $this->assertEquals($expectedResult, $this->sut->handleCommand($this->command)->toArray());
    }

    private function adapterCheckValid(int $returnStatus): void
    {
        $return = ['status' => $returnStatus];

        $this->adapter
            ->expects('confirmPasswordResetValid')
            ->with($this->username, $this->confirmationId, $this->tokenId)
            ->andReturn($return);
    }

    private function adapterResetAttempt(int $returnStatus): void
    {
        $return = ['status' => $returnStatus];

        $this->adapter
            ->expects('resetPassword')
            ->with($this->username, $this->confirmationId, $this->tokenId, $this->password)
            ->andReturn($return);
    }

    private function expectedResult(string $message, $hasExpired = false, $success = false): array
    {
        return [
            'id' => [],
            'flags' => [
                'hasExpired' => $hasExpired,
                'success' => $success,
            ],
            'messages' => [
                0 => $message,
            ],
        ];
    }

    private function getCommand(): ResetPasswordOpenAmCmd
    {
        $cmdData = [
            'username' => $this->username,
            'password' => $this->password,
            'confirmationId' => $this->confirmationId,
            'tokenId' => $this->tokenId,
            'realm' => $this->realm,
        ];

        return ResetPasswordOpenAmCmd::create($cmdData);
    }
}
