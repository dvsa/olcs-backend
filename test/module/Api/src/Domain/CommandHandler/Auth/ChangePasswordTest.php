<?php
declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\CommandHandler\Auth\ChangePassword;
use Dvsa\Olcs\Auth\Exception\ChangePasswordException;
use Dvsa\Olcs\Transfer\Command\Auth\ChangePassword as ChangePasswordCmd;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\Http\Response;
use Mockery as m;
use ZfcRbac\Identity\IdentityInterface;
use ZfcRbac\Service\AuthorizationService;

/**
 * @see ChangePassword
 */
class ChangePasswordTest extends CommandHandlerTestCase
{
    private m\MockInterface $adapter;
    private string $username = 'username';
    private string $oldPassword = 'oldPassword';
    private string $newPassword = 'newPassword';
    private string $realm = 'realm';
    private ChangePasswordCmd $command;

    public function setUp(): void
    {
        $this->adapter = m::mock(ValidatableAdapterInterface::class);
        $this->adapter->expects('setRealm')->with($this->realm);

        $this->mockedSmServices = [
            ValidatableAdapterInterface::class => $this->adapter,
            AuthorizationService::class => $this->setUpAuthService(),
        ];

        $this->command = $this->getCommand();
        $this->sut = new ChangePassword($this->adapter);

        parent::setUp();
    }

    public function setUpAuthService(): m\MockInterface
    {
        $mockUser = m::mock(User::class);
        $mockUser->expects('getLoginId')->withNoArgs()->andReturn($this->username);

        $identity = m::mock(IdentityInterface::class);
        $identity->expects('getUser')->withNoArgs()->andReturn($mockUser);

        $authService = m::mock(AuthorizationService::class);
        $authService->expects('getIdentity')->withNoArgs()->andReturn($identity);

        return $authService;
    }

    public function testHandleCommandSuccess(): void
    {
        $changeResult = [
            'status' => Response::STATUS_CODE_200
        ];

        $this->adapterResult($changeResult);

        $result = $this->sut->handleCommand($this->command);
        $this->assertEquals(ChangePassword::MSG_GENERIC_SUCCESS, $result->getMessages()[0]);
        $this->assertTrue($result->getFlag('success'));
    }

    /**
     * @dataProvider dpChangeResult
     */
    public function testHandleCommandFail(array $changeResult, string $failMessage): void
    {
        $this->adapterResult($changeResult);

        $result = $this->sut->handleCommand($this->command);
        $this->assertEquals($failMessage, $result->getMessages()[0]);
        $this->assertFalse($result->getFlag('success'));
    }

    public function dpChangeResult(): array
    {
        $withReason = [
            'status' => Response::STATUS_CODE_500,
            'reason' => 'reason',
        ];

        $withoutReason = [
            'status' => Response::STATUS_CODE_500,
        ];

        return [
            [$withReason, 'reason'],
            [$withoutReason, ChangePassword::MSG_GENERIC_FAIL],
        ];
    }

    public function testHandleCommandHandlesException(): void
    {
        $this->adapter->expects('changePassword')
            ->with($this->username, $this->oldPassword, $this->newPassword)
            ->andThrow(ChangePasswordException::class);

        $result = $this->sut->handleCommand($this->command);
        $this->assertEquals(ChangePassword::MSG_GENERIC_FAIL, $result->getMessages()[0]);
        $this->assertFalse($result->getFlag('success'));
    }

    private function adapterResult($changeResult): void
    {
        $this->adapter->expects('changePassword')
            ->with($this->username, $this->oldPassword, $this->newPassword)
            ->andReturn($changeResult);
    }

    private function getCommand(): ChangePasswordCmd
    {
        $cmdData = [
            'username' => $this->username,
            'password' => $this->oldPassword,
            'newPassword' => $this->newPassword,
            'realm' => $this->realm,
        ];

        return ChangePasswordCmd::create($cmdData);
    }
}
