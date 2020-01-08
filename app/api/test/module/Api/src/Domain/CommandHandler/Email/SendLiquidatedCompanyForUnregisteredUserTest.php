<?php declare(strict_types=1);

namespace module\Api\src\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Email\SendLiquidatedCompanyForUnregisteredUser as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendLiquidatedCompanyForUnregisteredUser;
use Dvsa\Olcs\Email\Data\Message;
use Dvsa\Olcs\Email\Domain\Command\SendEmail;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

class SendLiquidatedCompanyForUnregisteredUserTest extends CommandHandlerTestCase
{
    /** @var CommandInterface|SendLiquidatedCompanyForUnregisteredUser */
    protected $sut;

    public function setUp()
    {
        $this->sut = new SendLiquidatedCompanyForUnregisteredUser();

        $this->mockedSmServices = [
            TemplateRenderer::class => m::mock(TemplateRenderer::class),
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $emailAddress = 'toemail@example.com';
        $command = Cmd::create([
            'emailAddress' => $emailAddress,
            'translateToWelsh' => 'N'
        ]);

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->with(
            m::type(Message::class),
            'insolvent-company-notification_unregistered-user',
            [],
            'default'
        );

        $this->expectedSideEffect(
            SendEmail::class,
            [
                'to' => 'toemail@example.com',
                'subject' => 'email.insolvent-company-notification.subject'
            ],
            new Result()
        );

        /** @var Result $result */
        $result = $this->sut->handleCommand($command);

        $this->assertSame(['Email sent'], $result->getMessages());
    }
}
