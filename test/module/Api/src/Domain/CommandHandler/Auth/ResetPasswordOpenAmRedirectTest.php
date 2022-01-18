<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\Command\Auth\ResetPasswordOpenAm;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Auth\ResetPassword;
use Dvsa\Olcs\Auth\Adapter\OpenAm;
use Dvsa\Olcs\Transfer\Command\Auth\ResetPassword as ResetPasswordCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * @see ResetPassword
 * @todo this test can go once OpenAm is removed
 */
class ResetPasswordOpenAmRedirectTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $adapter = m::mock(OpenAm::class);
        $this->sut = new ResetPassword($adapter);

        parent::setUp();
    }

    public function testHandleCommandRedirectOpenAm(): void
    {
        $cmdData = [
            'username' => 'user',
            'password' => 'password',
            'realm' => 'realm',
            'confirmationId' => 'confirmation',
            'tokenId' => 'token',
        ];

        $command = ResetPasswordCmd::create($cmdData);

        $openAmResult = new Result();
        $this->expectedSideEffect(ResetPasswordOpenAm::class, $cmdData, $openAmResult);
        $this->assertEquals($openAmResult, $this->sut->handleCommand($command));
    }
}
