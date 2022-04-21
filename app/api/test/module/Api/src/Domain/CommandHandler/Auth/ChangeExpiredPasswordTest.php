<?php
declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\CommandHandler\Auth\ChangeExpiredPassword;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\Repository\User;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Transfer\Command\Auth\ChangeExpiredPassword as ChangeExpiredPasswordCmd;
use Dvsa\Olcs\Transfer\Result\Auth\ChangeExpiredPasswordResult;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\Authentication\Result;
use Mockery as m;

/**
 * @see ChangeExpiredPassword
 */
class ChangeExpiredPasswordTest extends CommandHandlerTestCase
{
    private m\MockInterface $adapter;
    private string $newPassword = 'newPassword';
    private string $challengeSession = 'challengeSession';
    private string $username = 'username';

    private ChangeExpiredPasswordCmd $command;

    /**
     * @var User|m\LegacyMockInterface|m\MockInterface
     */
    private m\MockInterface $mockUserRepo;

    public function setUp(): void
    {
        $this->adapter = m::mock(ValidatableAdapterInterface::class);

        $this->mockUserRepo();

        $this->command = $this->getCommand();
        $this->sut = new ChangeExpiredPassword($this->adapter, $this->mockUserRepo);

        parent::setUp();
    }

    /**
     * @dataProvider dpHandleCommand
     */
    public function testHandleCommand(int $code, array $messages, $isSuccess): void
    {
        $identity = ['identity'];
        $changeResult = new ChangeExpiredPasswordResult($code, $identity, ['aws-messages']);
        $this->adapterResult($changeResult);

        $result = $this->sut->handleCommand($this->command);
        $this->assertEquals($isSuccess, $result->getFlag('isValid'));
        $this->assertEquals($code, $result->getFlag('code'));
        $this->assertSame($identity, $result->getFlag('identity'));
        $this->assertSame($messages, $result->getFlag('messages'));
    }

    public function dpHandleCommand()
    {
        return [
            [ChangeExpiredPasswordResult::SUCCESS, [0 => ChangeExpiredPassword::MSG_GENERIC_SUCCESS], true],
            [ChangeExpiredPasswordResult::SUCCESS_WITH_CHALLENGE, [0 => ChangeExpiredPassword::MSG_GENERIC_SUCCESS], true],
            [ChangeExpiredPasswordResult::FAILURE_NEW_PASSWORD_INVALID, [0 => ChangeExpiredPassword::MSG_INVALID], false],
            [ChangeExpiredPasswordResult::FAILURE_NOT_AUTHORIZED, [0 => ChangeExpiredPassword::MSG_NOT_AUTHORIZED], false],
            [ChangeExpiredPasswordResult::FAILURE, [0 => ChangeExpiredPassword::MSG_GENERIC_FAIL], false],
            [ChangeExpiredPasswordResult::FAILURE_CLIENT_ERROR, [0 => ChangeExpiredPassword::MSG_GENERIC_FAIL], false],
        ];
    }

    public function testHandleCommandUpdatesUserLastLoginAtOnSuccessResult(): void
    {
        $changeResult = new ChangeExpiredPasswordResult(ChangeExpiredPasswordResult::SUCCESS, [], []);
        $this->adapterResult($changeResult);

        $mockUser = m::mock(UserEntity::class);
        $mockUser->shouldReceive('setLastLoginAt')
            ->once();

        $this->mockUserRepo->shouldReceive('fetchByLoginId')->andReturn([$mockUser]);

        $this->sut->handleCommand($this->command);
    }

    /**
     * @dataProvider resultsDataProvider
     */
    public function testHandleCommandDoesNotUpdateUserLastLoginAtOnNonSuccessResult(int $result): void
    {
        $changeResult = new ChangeExpiredPasswordResult($result, [], []);
        $this->adapterResult($changeResult);

        $this->mockUserRepo->shouldNotReceive('fetchByLoginId');

        $this->sut->handleCommand($this->command);
    }

    public function testHandleCommandThrowsExceptionWhenUserCannotBeFound()
    {
        $changeResult = new ChangeExpiredPasswordResult(ChangeExpiredPasswordResult::SUCCESS, [], []);
        $this->adapterResult($changeResult);

        $this->mockUserRepo->shouldReceive('fetchByLoginId')->andReturn([]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(ChangeExpiredPassword::ERROR_USER_NOT_FOUND);

        $this->sut->handleCommand($this->command);
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

    private function mockUserRepo(): void
    {
        $instance = m::mock(User::class);
        $mockUser = m::mock(UserEntity::class)->shouldIgnoreMissing();
        $instance->allows('fetchByLoginId')
            ->andReturns([$mockUser])
            ->byDefault();
        $instance->shouldIgnoreMissing();

        $this->mockUserRepo = $instance;
    }

    public function resultsDataProvider(): array
    {
        return [
            'Challenge result' => [ChangeExpiredPasswordResult::SUCCESS_WITH_CHALLENGE],
            'Failure result' => [ChangeExpiredPasswordResult::FAILURE],
            'Invalid password result' => [ChangeExpiredPasswordResult::FAILURE_NEW_PASSWORD_INVALID],
            'Client error result' => [ChangeExpiredPasswordResult::FAILURE_CLIENT_ERROR],
            'Not authorized result' => [ChangeExpiredPasswordResult::FAILURE_NOT_AUTHORIZED],
        ];
    }
}
