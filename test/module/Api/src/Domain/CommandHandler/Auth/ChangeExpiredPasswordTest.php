<?php
declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\CommandHandler\Auth\ChangeExpiredPassword;
use Dvsa\Olcs\Transfer\Command\Auth\ChangeExpiredPassword as ChangeExpiredPasswordCmd;
use Dvsa\Olcs\Transfer\Result\Auth\ChangeExpiredPasswordResult;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\Authentication\Result;
use Mockery as m;

class ChangeExpiredPasswordTest extends CommandHandlerTestCase
{
    private m\MockInterface $adapter;
    private string $newPassword = 'newPassword';
    private string $challengeSession = 'challengeSession';
    private string $username = 'username';

    private ChangeExpiredPasswordCmd $command;

    public function setUp(): void
    {
        $this->adapter = m::mock(ValidatableAdapterInterface::class);

        $this->mockedSmServices = [
            ValidatableAdapterInterface::class => $this->adapter,
        ];

        $this->command = $this->getCommand();
        $this->sut = new ChangeExpiredPassword($this->adapter);

        parent::setUp();
    }

    public function testHandleCommand(): void
    {
        $changeResult = new ChangeExpiredPasswordResult(ChangeExpiredPasswordResult::SUCCESS, $identity = [], $messages = ['example']);
        $this->adapterResult($changeResult);

        $result = $this->sut->handleCommand($this->command);
        $this->assertTrue($result->getFlag('isValid'));
        $this->assertEquals(Result::SUCCESS, $result->getFlag('code'));
        $this->assertSame($identity, $result->getFlag('identity'));
        $this->assertSame($messages, $result->getFlag('messages'));
    }

    private function adapterResult($changeResult): void
    {
        $this->adapter->expects('changeExpiredPassword')
            ->with($this->newPassword, $this->challengeSession, $this->username)
            ->andReturn($changeResult);
    }

    private function getCommand(): ChangeExpiredPasswordCmd
    {
        $cmdData = [
            'newPassword' => $this->newPassword,
            'challengeSession' => $this->challengeSession,
            'username' => $this->username,
        ];

        return ChangeExpiredPasswordCmd::create($cmdData);
    }
}
