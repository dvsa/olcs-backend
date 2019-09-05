<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\CommandHandlerInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEcmtAppSubmitted;
use Dvsa\Olcs\Api\Domain\Exception\MissingEmailException;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as PermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepo;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Email\Data\Message;
use Dvsa\Olcs\Email\Domain\Command\SendEmail;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Abstract ECMT annual permit email tester
 */
abstract class AbstractEcmtAnnualPermitTest extends AbstractPermitTest
{
    /**
     * test handle command
     */
    public function testHandleCommand()
    {
        $templateVars = [
            'appUrl' => 'http://selfserve/',
            'permitsUrl' => 'http://selfserve/permits',
            'guidanceUrl' => 'https://www.gov.uk/guidance/international-authorisations-and-permits-for-road-haulage',
            'ecmtGuidanceUrl' => 'https://www.gov.uk/guidance/ecmt-international-road-haulage-permits',
            'applicationRef' => $this->applicationRef,
            'applicationFee' => '10',
        ];

        $this->applicationEntity->shouldReceive('isAwaitingFee')->once()->withNoArgs()->andReturn(false);
        $this->applicationEntity->shouldReceive('getCreatedBy')->once()->withNoArgs()->andReturn($this->userEntity);

        $this->organisation->shouldReceive('getAdminEmailAddresses')->once()->andReturn($this->orgEmails);

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->once()->with(
            m::type(Message::class),
            $this->template,
            $templateVars,
            'default'
        );

        $applicationFee = '10.00';

        $feeTypeEntity = m::mock(FeeType::class);
        $feeTypeEntity->shouldReceive('getAmount')
            ->withNoArgs()
            ->andReturn($applicationFee);
        $this->repoMap['FeeType']->shouldReceive('getLatestByProductReference')
            ->once()
            ->with(FeeType::FEE_TYPE_ECMT_APP_PRODUCT_REF)
            ->andReturn($feeTypeEntity);

        $this->contactDetails->shouldReceive('getEmailAddress')->once()->withNoArgs()->andReturn($this->userEmail);
        $this->userEntity->shouldReceive('getContactDetails')->once()->withNoArgs()->andReturn($this->contactDetails);
        $this->expectedSideEffect(SendEmail::class, $this->data, new Result());

        $result = $this->sut->handleCommand($this->commandEntity);

        $this->assertSame(['EcmtPermitApplication' => $this->permitAppId], $result->getIds());
        $this->assertSame(['Email sent'], $result->getMessages());

        /** @var Message $message */
        $message = $this->sut->getMessage();
        $this->assertSame($this->userEmail, $message->getTo());
        $this->assertSame($this->orgEmails, $message->getCc());
        $this->assertSame($this->subject, $message->getSubject());
    }

    /**
     * test handle command awaiting fee
     */
    public function testHandleCommandAwaitingFee()
    {
        $permitsRequired = 2;
        $permitsAwarded = 2;
        $issueFeeAmount = 123;
        $permitsGrantedissueFeeTotal = $permitsRequired * $issueFeeAmount;
        $paymentDeadlineNumDays = '10';
        $issueFeeDeadlineDate = '21 March 2019';
        $awaitingFeeUrl = 'http://selfserve/permits/' . $this->permitAppId . '/ecmt-awaiting-fee/';
        $validityYear = '2022';

        $templateVars = [
            'appUrl' => 'http://selfserve/',
            'permitsUrl' => 'http://selfserve/permits',
            'guidanceUrl' => 'https://www.gov.uk/guidance/international-authorisations-and-permits-for-road-haulage',
            'ecmtGuidanceUrl' => 'https://www.gov.uk/guidance/ecmt-international-road-haulage-permits',
            'applicationRef' => $this->applicationRef,
            'awaitingFeeUrl' => $awaitingFeeUrl,
            'permitsRequired' => $permitsRequired,
            'permitsGranted' => $permitsRequired,
            'paymentDeadlineNumDays' => $paymentDeadlineNumDays,
            'issueFeeDeadlineDate' => $issueFeeDeadlineDate,
            'issueFeeAmount' => $issueFeeAmount,
            'issueFeeTotal' => $permitsGrantedissueFeeTotal,
            'applicationFee' => '10',
            'validityYear' => $validityYear,
        ];

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('first')->andReturn($irhpPermitApplication);
        $irhpPermitApplication->shouldReceive('calculateTotalPermitsRequired')->andReturn($permitsRequired);
        $irhpPermitApplication->shouldReceive('countPermitsAwarded')->andReturn($permitsAwarded);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getValidityYear')
            ->andReturn($validityYear);

        $fee = m::mock(Fee::class);
        $fee->shouldReceive('isEcmtIssuingFee')->andReturn(true);
        $fee->shouldReceive('getFeeTypeAmount')->andReturn($issueFeeAmount);
        $fee->shouldReceive('getOutstandingAmount')->andReturn($permitsGrantedissueFeeTotal);
        $fee->shouldReceive('getInvoicedDateTime')->andReturn(
            new DateTime('8 March 2019')
        );
        $fees = [$fee];

        $this->applicationEntity->shouldReceive('getIrhpPermitApplications')->andReturn($irhpPermitApplication);
        $this->applicationEntity->shouldReceive('calculateTotalPermitsRequired')->andReturn($permitsRequired);
        $this->applicationEntity->shouldReceive('getFees->matching')->andReturn($fees);
        $this->applicationEntity->shouldReceive('isAwaitingFee')->once()->withNoArgs()->andReturn(true);
        $this->applicationEntity->shouldReceive('getCreatedBy')->once()->withNoArgs()->andReturn($this->userEntity);

        $this->organisation->shouldReceive('getAdminEmailAddresses')->once()->andReturn($this->orgEmails);

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->once()->with(
            m::type(Message::class),
            $this->template,
            $templateVars,
            'default'
        );

        $applicationFee = '10.00';

        $feeTypeEntity = m::mock(FeeType::class);
        $feeTypeEntity->shouldReceive('getAmount')
            ->withNoArgs()
            ->andReturn($applicationFee);
        $this->repoMap['FeeType']->shouldReceive('getLatestByProductReference')
            ->once()
            ->with(FeeType::FEE_TYPE_ECMT_APP_PRODUCT_REF)
            ->andReturn($feeTypeEntity);

        $this->contactDetails->shouldReceive('getEmailAddress')->once()->withNoArgs()->andReturn($this->userEmail);
        $this->userEntity->shouldReceive('getContactDetails')->once()->withNoArgs()->andReturn($this->contactDetails);
        $this->expectedSideEffect(SendEmail::class, $this->data, new Result());

        $result = $this->sut->handleCommand($this->commandEntity);

        $this->assertSame(['EcmtPermitApplication' => $this->permitAppId], $result->getIds());
        $this->assertSame(['Email sent'], $result->getMessages());

        /** @var Message $message */
        $message = $this->sut->getMessage();
        $this->assertSame($this->userEmail, $message->getTo());
        $this->assertSame($this->orgEmails, $message->getCc());
        $this->assertSame($this->subject, $message->getSubject());
    }

    /**
     * test the exception is dealt with when there are no email addresses
     */
    public function testHandleCommandException()
    {
        $this->organisation->shouldReceive('getAdminEmailAddresses')->once()->withNoArgs()->andReturn([]);
        $this->applicationEntity->shouldReceive('getCreatedBy')->once()->withNoArgs()->andReturn(null);

        $result = $this->sut->handleCommand($this->commandEntity);

        $this->assertSame(['EcmtPermitApplication' => $this->permitAppId], $result->getIds());
        $this->assertSame([MissingEmailException::MSG_NO_ORG_EMAIL], $result->getMessages());
        $this->assertNull($this->sut->getMessage());
    }
}
