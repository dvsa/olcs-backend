<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\Command\Auth\ForgotPasswordOpenAm as ForgotPasswordOpenAmCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Auth\ForgotPasswordOpenAm;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\Http\Response;
use Mockery as m;

/**
 * @see ForgotPasswordOpenAm
 */
class ForgotPasswordOpenAmTest extends CommandHandlerTestCase
{
    private m\MockInterface $adapter;
    private m\MockInterface $translator;
    private string $username = 'username';
    private string $realm = 'realm';
    private string $translatedSubject = 'translated subject';
    private string $translatedMessage = 'translated message';
    private ForgotPasswordOpenAmCmd $command;

    public function setUp(): void
    {
        $this->adapter = m::mock(ValidatableAdapterInterface::class);
        $this->adapter->expects('setRealm')->with($this->realm);

        $this->translator = m::mock(TranslatorDelegator::class);
        $this->translator
            ->expects('translate')
            ->with(ForgotPasswordOpenAm::EMAIL_SUBJECT_KEY)
            ->andReturn($this->translatedSubject);
        $this->translator
            ->expects('translate')
            ->with(ForgotPasswordOpenAm::EMAIL_MESSAGE_KEY)
            ->andReturn($this->translatedMessage);

        $this->command = $this->getCommand();
        $this->sut = new ForgotPasswordOpenAm($this->adapter, $this->translator);

        parent::setUp();
    }

    public function testHandleCommandFail(): void
    {
        $this->adapterAttempt(Response::STATUS_CODE_500);
        $expectedResult = $this->expectedResult(ForgotPasswordOpenAm::MSG_GENERIC_FAIL);
        $this->assertEquals($expectedResult, $this->sut->handleCommand($this->command)->toArray());
    }

    public function testHandleCommandException(): void
    {
        $this->adapter
            ->expects('forgotPassword')
            ->with($this->username, $this->translatedSubject, $this->translatedMessage)
            ->andThrow(new \Exception());
        $expectedResult = $this->expectedResult(ForgotPasswordOpenAm::MSG_GENERIC_FAIL);
        $this->assertEquals($expectedResult, $this->sut->handleCommand($this->command)->toArray());
    }

    public function testHandleCommandResetSuccess(): void
    {
        $this->adapterAttempt(Response::STATUS_CODE_200);
        $expectedResult = $this->expectedResult(ForgotPasswordOpenAm::MSG_GENERIC_SUCCESS, true);
        $this->assertEquals($expectedResult, $this->sut->handleCommand($this->command)->toArray());
    }

    private function adapterAttempt(int $returnStatus): void
    {
        $return = ['status' => $returnStatus];

        $this->adapter
            ->expects('forgotPassword')
            ->with($this->username, $this->translatedSubject, $this->translatedMessage)
            ->andReturn($return);
    }

    private function expectedResult(string $message, bool $success = false): array
    {
        return [
            'id' => [],
            'flags' => [
                'success' => $success,
            ],
            'messages' => [
                0 => $message,
            ],
        ];
    }

    private function getCommand():ForgotPasswordOpenAmCmd
    {
        $cmdData = [
            'username' => $this->username,
            'realm' => $this->realm,
        ];

        return ForgotPasswordOpenAmCmd::create($cmdData);
    }
}
