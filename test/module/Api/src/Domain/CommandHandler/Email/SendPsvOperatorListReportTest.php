<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendPsvOperatorListReport;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;
use Dvsa\Olcs\Email\Domain\Command\SendEmail;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Mockery as m;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrErrors as Cmd;
use Dvsa\Olcs\Email\Data\Message;
use Dvsa\Olcs\Api\Domain\Repository\SystemParameter as SystemParameterRepo;

/**
 * Send Psv Operator List Report Test
 */
class SendPsvOperatorListReportTest extends CommandHandlerTestCase
{
    /**
     * @var CommandInterface|SendPsvOperatorListReport
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new SendPsvOperatorListReport();
        $this->mockRepo('SystemParameter', SystemParameterRepo::class);

        $this->mockedSmServices = [
            TemplateRenderer::class => m::mock(TemplateRenderer::class),
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(['id' => 101]);

        // expectations
        $this->repoMap['SystemParameter']
            ->shouldReceive('fetchValue')
            ->with(SystemParameter::PSV_REPORT_EMAIL_LIST)
            ->andReturn('testdummy@dummyemail.com');

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->with(
            m::type(Message::class),
            SendPsvOperatorListReport::EMAIL_TEMPLATE,
            [],
            'default'
        );

        $this->expectedSideEffect(
            SendEmail::class,
            [
                'to' => 'testdummy@dummyemail.com'
            ],
            new Result()
        );

        /** @var Result $result */
        $result = $this->sut->handleCommand($command);

        $this->assertSame(101, $result->getId('document'));
        $this->assertSame(['PSV Operator list sent to: testdummy@dummyemail.com'], $result->getMessages());
    }

    public function testNoEmailThrowException()
    {
        $this->expectException(
            \InvalidArgumentException::class,
            'No email address specified in system parameters for the PSV Report'
        );

        $command = Cmd::create(['id' => 101]);

        // expectations
        $this->repoMap['SystemParameter']
            ->shouldReceive('fetchValue')
            ->with(SystemParameter::PSV_REPORT_EMAIL_LIST)
            ->andReturnNull();

        $this->sut->handleCommand($command);
    }
}
