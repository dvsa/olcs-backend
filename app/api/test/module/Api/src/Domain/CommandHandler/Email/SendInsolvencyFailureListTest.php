<?php

namespace module\Api\src\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Email\SendInsolvencyFailureList as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendInsolvencyFailureList;
use Dvsa\Olcs\Email\Domain\Command\SendEmail;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

class SendInsolvencyFailureListTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new SendInsolvencyFailureList();
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $emailAddress = 'toemail@example.com';
        $command = Cmd::create([
            'organisationNumbers' => ['0000', '1111', '2222', '3333'],
            'emailAddress' => $emailAddress,
            'emailSubject' => 'Test email'
        ]);

        $result = new Result();
        $result->addMessage('Email sent');
        $this->expectedSideEffect(
            SendEmail::class,
            [
                'to' => 'toemail@example.com',
                'subject' => 'Test email',
                'plainBody' => "0000\n1111\n2222\n3333"
            ],
            $result
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['Email sent'], $result->getMessages());
    }
}
