<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use DateTime;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtShortTermSuccessful as SendEcmtShortTermSuccessfulCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEcmtShortTermSuccessful as SendEcmtShortTermSuccessfulHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Email\Data\Message;
use Dvsa\Olcs\Email\Domain\Command\SendEmail;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Mockery as m;

/**
 * Test the short term permit app successful email
 */
class SendEcmtShortTermSuccessfulTest extends AbstractPermitTest
{
    protected $commandClass = SendEcmtShortTermSuccessfulCmd::class;
    protected $commandHandlerClass = SendEcmtShortTermSuccessfulHandler::class;
    protected $template = 'ecmt-short-term-app-successful';
    protected $subject = 'email.ecmt.short.term.response.subject';
    protected $permitApplicationRepo = 'IrhpApplication';
    protected $applicationEntityClass = IrhpApplication::class;

    /**
     * test handle command
     */
    public function testHandleCommand()
    {
        $euro5PermitsGranted = 3;
        $euro6PermitsGranted = 8;

        $issueFeeAmount = '10.00';
        $issueFeeAmountFormatted = '10';
        $issueFeeTotal = '110.00';
        $issueFeeTotalFormatted = '110';

        $issueFee = m::mock(Fee::class);
        $issueFee->shouldReceive('getFeeTypeAmount')
            ->andReturn($issueFeeAmount);
        $issueFee->shouldReceive('getOutstandingAmount')
            ->andReturn($issueFeeTotal);
        $issueFee->shouldReceive('getInvoicedDateTime')
            ->andReturn(new DateTime('8 March 2019'));

        $templateVars = [
            'applicationRef' => $this->applicationRef,
            'euro5PermitsGranted' => $euro5PermitsGranted,
            'euro6PermitsGranted' => $euro6PermitsGranted,
            'issueFeeAmount' => $issueFeeAmountFormatted,
            'issueFeeTotal' => $issueFeeTotalFormatted,
            'paymentDeadlineNumDays' => '10',
            'issueFeeDeadlineDate' => '21 March 2019',
            'paymentUrl' => 'http://selfserve/permits/application/' . $this->permitAppId . '/awaiting-fee',
        ];

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->once()->with(
            m::type(Message::class),
            $this->template,
            $templateVars,
            'default'
        );

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getRequiredEuro5')
            ->andReturn($euro5PermitsGranted);
        $irhpPermitApplication->shouldReceive('getRequiredEuro6')
            ->andReturn($euro6PermitsGranted);

        $this->repoMap['IrhpApplication']->shouldReceive('refresh')
            ->with($this->applicationEntity)
            ->once()
            ->globally()
            ->ordered();

        $this->applicationEntity->shouldReceive('getCreatedBy')->once()->withNoArgs()->andReturn($this->userEntity);
        $this->applicationEntity->shouldReceive('getFirstIrhpPermitApplication')
            ->andReturn($irhpPermitApplication);
        $this->applicationEntity->shouldReceive('getLatestIssueFee')
            ->withNoArgs()
            ->once()
            ->globally()
            ->ordered()
            ->andReturn($issueFee);

        $this->organisation->shouldReceive('getAdminEmailAddresses')->once()->andReturn($this->orgEmails);

        $this->contactDetails->shouldReceive('getEmailAddress')->once()->withNoArgs()->andReturn($this->userEmail);
        $this->userEntity->shouldReceive('getContactDetails')->once()->withNoArgs()->andReturn($this->contactDetails);
        $this->expectedSideEffect(SendEmail::class, $this->data, new Result());

        $result = $this->sut->handleCommand($this->commandEntity);

        $this->assertSame(['IrhpApplication' => $this->permitAppId], $result->getIds());
        $this->assertSame(['Email sent'], $result->getMessages());

        /** @var Message $message */
        $message = $this->sut->getMessage();
        $this->assertSame($this->userEmail, $message->getTo());
        $this->assertSame($this->orgEmails, $message->getCc());
        $this->assertSame($this->subject, $message->getSubject());
    }
}
