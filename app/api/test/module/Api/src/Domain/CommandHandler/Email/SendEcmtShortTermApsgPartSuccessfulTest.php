<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use DateTime;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtShortTermApsgPartSuccessful as SendEcmtShortTermApsgPartSuccessfulCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEcmtShortTermApsgPartSuccessful as SendEcmtShortTermApsgPartSuccessfulHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Email\Data\Message;
use Dvsa\Olcs\Email\Domain\Command\SendEmail;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Mockery as m;

/**
 * Test the short term permit app part successful email
 */
class SendEcmtShortTermApsgPartSuccessfulTest extends AbstractPermitTest
{
    protected $commandClass = SendEcmtShortTermApsgPartSuccessfulCmd::class;
    protected $commandHandlerClass = SendEcmtShortTermApsgPartSuccessfulHandler::class;
    protected $template = 'ecmt-short-term-app-part-successful';
    protected $subject = 'email.ecmt.short.term.response.subject';
    protected $permitApplicationRepo = 'IrhpApplication';
    protected $applicationEntityClass = IrhpApplication::class;

    /**
     * @dataProvider dpTranslateToWelshLocaleMappings
     */
    public function testHandleCommand($translateToWelsh, $expectedLocale)
    {
        $previousLocale = 'aa_DJ';
        $periodNameKey = 'period.name.translation.key';
        $periodName = 'Permits for journeys in January and Feburary 2020';

        $euro5PermitsRequired = 10;
        $euro6PermitsRequired = 11;
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
            'euro5PermitsRequired' => $euro5PermitsRequired,
            'euro6PermitsRequired' => $euro6PermitsRequired,
            'euro5PermitsGranted' => $euro5PermitsGranted,
            'euro6PermitsGranted' => $euro6PermitsGranted,
            'issueFeeAmount' => $issueFeeAmountFormatted,
            'issueFeeTotal' => $issueFeeTotalFormatted,
            'paymentDeadlineNumDays' => '10',
            'issueFeeDeadlineDate' => '21 March 2019',
            'paymentUrl' => 'http://selfserve/permits/application/' . $this->permitAppId . '/awaiting-fee',
            'periodName' => 'Permits for journeys in January and Feburary 2020',
        ];

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->once()->with(
            m::type(Message::class),
            $this->template,
            $templateVars,
            'default'
        );

        $irhpPermitStock = m::mock(IrhpPermitStock::class);
        $irhpPermitStock->shouldReceive('getPeriodNameKey')
            ->andReturn($periodNameKey);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->andReturn($irhpPermitStock);
        $irhpPermitApplication->shouldReceive('countPermitsAwarded')
            ->with(RefData::EMISSIONS_CATEGORY_EURO5_REF)
            ->andReturn($euro5PermitsGranted);
        $irhpPermitApplication->shouldReceive('countPermitsAwarded')
            ->with(RefData::EMISSIONS_CATEGORY_EURO6_REF)
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

        $irhpPermitApplication->shouldReceive('getRequiredEuro5')
            ->andReturn($euro5PermitsRequired);
        $irhpPermitApplication->shouldReceive('getRequiredEuro6')
            ->andReturn($euro6PermitsRequired);

        $this->mockedSmServices['translator']->shouldReceive('getLocale')
            ->withNoArgs()
            ->once()
            ->andReturn($previousLocale)
            ->globally()
            ->ordered();
        $this->mockedSmServices['translator']->shouldReceive('setLocale')
            ->with($expectedLocale)
            ->once()
            ->globally()
            ->ordered();
        $this->mockedSmServices['translator']->shouldReceive('translate')
            ->with($periodNameKey, 'snapshot')
            ->once()
            ->globally()
            ->ordered()
            ->andReturn($periodName);
        $this->mockedSmServices['translator']->shouldReceive('setLocale')
            ->with($previousLocale)
            ->once()
            ->globally()
            ->ordered();

        $this->applicationEntity->shouldReceive('getLicence->getTranslateToWelsh')
            ->withNoArgs()
            ->andReturn($translateToWelsh);

        $expectedData = [
            'to' => $this->userEmail,
            'locale' => $expectedLocale,
            'subject' => $this->subject,
        ];

        $this->expectedSideEffect(SendEmail::class, $expectedData, new Result());

        $result = $this->sut->handleCommand($this->commandEntity);

        $this->assertSame(['IrhpApplication' => $this->permitAppId], $result->getIds());
        $this->assertSame(['Email sent'], $result->getMessages());

        /** @var Message $message */
        $message = $this->sut->getMessage();
        $this->assertSame($this->userEmail, $message->getTo());
        $this->assertSame($this->orgEmails, $message->getCc());
        $this->assertSame($this->subject, $message->getSubject());
    }

    public function dpTranslateToWelshLocaleMappings()
    {
        return [
            ['Y', 'cy_GB'],
            ['N', 'en_GB'],
        ];
    }
}
