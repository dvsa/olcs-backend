<?php

/**
 * Send Email Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Email\Domain\CommandHandler;

use Dvsa\Olcs\Email\Service\Email;
use Mockery as m;
use Dvsa\Olcs\Email\Domain\CommandHandler\SendEmail;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Zend\I18n\Translator\Translator;
use Dvsa\Olcs\Email\Domain\Command\SendEmail as Cmd;

/**
 * Send Email Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SendEmailTest extends CommandHandlerTestCase
{
    /**
     * @var SendEmail
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new SendEmail();

        $this->mockedSmServices['Config'] = [
            'email' => [
                'from_name' => 'Terry',
                'from_email' => 'terry.valtech@gmail.com',
                'send_all_mail_to' => 'terry.valtech@gmail.com',
                'selfserve_uri' => 'olcs-selfserve'
            ]
        ];

        $this->mockedSmServices['translator'] = m::mock(Translator::class);
        $this->mockedSmServices['translator']->shouldReceive('translate')->andReturnUsing(
            function ($message) {
                return 'translated-' . $message;
            }
        );

        $this->mockedSmServices['EmailService'] = m::mock(Email::class);

        parent::setUp();
    }

    public function testHandleCommandEmptyBody()
    {
        $this->setExpectedException(\RuntimeException::class);

        $data = [
            'body' => ''
        ];

        $command = Cmd::create($data);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommand()
    {
        $data = [
            'to' => 'foo@bar.com',
            'cc' => ['bar@foo.com'],
            'subject' => 'Some subject',
            'html' => false,
            'body' => 'This is the email'
        ];

        $command = Cmd::create($data);

        $this->mockedSmServices['EmailService']->shouldReceive('send')
            ->once()
            ->with(
                'terry.valtech@gmail.com',
                'Terry',
                'terry.valtech@gmail.com',
                'foo@bar.com : translated-Some subject',
                'translated-This is the email',
                false,
                ['bar@foo.com']
            );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandAlt()
    {
        $data = [
            'fromName' => 'Foo',
            'fromEmail' => 'foobar@cake.com',
            'to' => 'foo@bar.com',
            'cc' => ['bar@foo.com'],
            'subject' => 'Some subject',
            'html' => false,
            'body' => 'This is the email http://selfserve'
        ];

        $command = Cmd::create($data);

        $this->sut->setSendAllMailTo(null);

        $this->mockedSmServices['EmailService']->shouldReceive('send')
            ->once()
            ->with(
                'foobar@cake.com',
                'Foo',
                'foo@bar.com',
                'translated-Some subject',
                'translated-This is the email olcs-selfserve',
                false,
                ['bar@foo.com']
            );

        $this->sut->handleCommand($command);
    }
}
