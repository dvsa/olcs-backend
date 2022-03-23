<?php
declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\Command\Auth\ForgotPasswordOpenAm;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Auth\ForgotPassword;
use Dvsa\Olcs\Api\Domain\Repository\User as UserRepo;
use Dvsa\Olcs\Auth\Service\PasswordService;
use Dvsa\Olcs\Transfer\Command\Auth\ForgotPassword as ForgotPasswordCmd;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Mockery as m;

/**
 * @see ForgotPassword
 * @todo this test can go once OpenAm is removed
 */
class ForgotPasswordOpenAmRedirectTest extends CommandHandlerTestCase
{
    private string $username = 'username';
    private string $realm = 'realm';
    private ForgotPasswordCmd $command;

    public function setUp(): void
    {
        $this->mockRepo('User', UserRepo::class);

        $this->mockedSmServices = [
            'Config' => $this->getConfig(ForgotPassword::OPENAM_ADAPTER_CONFIG_VALUE),
        ];

        $this->command = $this->getCommand();
        $this->sut = new ForgotPassword(m::mock(ValidatableAdapterInterface::class)->shouldIgnoreMissing(), m::mock(PasswordService::class)->shouldIgnoreMissing());

        parent::setUp();
    }

    public function testHandleCommandRedirectOpenAm(): void
    {
        $user = m::mock(UserEntity::class);
        $user->expects('canResetPassword')->withNoArgs()->andReturnTrue();

        $this->repoMap['User']->expects('fetchEnabledIdentityByLoginId')->with($this->username)->andReturn($user);

        $openAmResult = new Result();
        $this->expectedSideEffect(ForgotPasswordOpenAm::class, $this->getCmdData(), $openAmResult);
        $this->assertEquals($openAmResult, $this->sut->handleCommand($this->command));
    }

    private function getConfig(string $adapter): array
    {
        return [
            'auth' => [
                'default_adapter' => $adapter
            ],
        ];
    }

    private function getCommand(): ForgotPasswordCmd
    {
        $cmdData = $this->getCmdData();
        return ForgotPasswordCmd::create($cmdData);
    }

    private function getCmdData(): array
    {
        return [
            'username' => $this->username,
            'realm' => $this->realm,
        ];
    }
}
