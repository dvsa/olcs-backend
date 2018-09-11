<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAppSubmitted as SendEcmtAppSubmittedCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEcmtAppSubmitted;
use Dvsa\Olcs\Api\Domain\Exception\MissingEmailException;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as PermitApplicationRepo;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Email\Data\Message;
use Dvsa\Olcs\Email\Domain\Command\SendEmail;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Test the permit app submitted email
 */
class SendEcmtAppSubmittedTest extends CommandHandlerTestCase
{
    /**
     * @var SendEcmtAppSubmitted
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new SendEcmtAppSubmitted();
        $this->mockRepo('EcmtPermitApplication', PermitApplicationRepo::class);

        $this->mockedSmServices = [
            TemplateRenderer::class => m::mock(TemplateRenderer::class),
        ];

        parent::setUp();
    }

    /**
     * test handle command
     */
    public function testHandleCommand()
    {
        $permitAppId = 1234;
        $applicationRef = 'OC1234567/1234';
        $command = SendEcmtAppSubmittedCmd::create(['id' => $permitAppId]);

        $userEmail = 'email1@test.com';
        $orgEmails = [
            'orgEmail1@test.com',
            'orgEmail2@test.com'
        ];

        $templateVars = [
            'url' => 'http://selfserve/',
            'applicationRef' => $applicationRef,
        ];

        $contactDetails = m::mock(ContactDetails::class);
        $contactDetails->shouldReceive('getEmailAddress')->once()->withNoArgs()->andReturn($userEmail);

        $userEntity = m::mock(User::class);
        $userEntity->shouldReceive('getContactDetails')->once()->withNoArgs()->andReturn($contactDetails);

        $organisation = m::mock(Organisation::class);
        $organisation->shouldReceive('getAdminEmailAddresses')->once()->andReturn($orgEmails);

        $applicationEntity = m::mock(EcmtPermitApplication::class);
        $applicationEntity->shouldReceive('getCreatedBy')->once()->withNoArgs()->andReturn($userEntity);
        $applicationEntity->shouldReceive('getApplicationRef')->once()->withNoArgs()->andReturn($applicationRef);
        $applicationEntity->shouldReceive('getId')->once()->withNoArgs()->andReturn($permitAppId);
        $applicationEntity->shouldReceive('getLicence->getOrganisation')
            ->once()
            ->withNoArgs()
            ->andReturn($organisation);

        $this->repoMap['EcmtPermitApplication']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->once()
            ->andReturn($applicationEntity);

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->once()->with(
            m::type(Message::class),
            SendEcmtAppSubmitted::TEMPLATE,
            $templateVars,
            'default'
        );

        $data = [
            'to' => $userEmail,
            'locale' => 'en_GB',
            'subject' => SendEcmtAppSubmitted::SUBJECT,
        ];

        $this->expectedSideEffect(SendEmail::class, $data, new Result());

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['ecmtPermitApplication' => $permitAppId], $result->getIds());
        $this->assertSame(['Email sent'], $result->getMessages());

        /** @var Message $message */
        $message = $this->sut->getMessage();
        $this->assertSame($userEmail, $message->getTo());
        $this->assertSame($orgEmails, $message->getCc());
        $this->assertSame(SendEcmtAppSubmitted::SUBJECT, $message->getSubject());
    }

    /**
     * test the exception is dealt with when there are no email addresses
     */
    public function testHandleCommandException()
    {
        $permitAppId = 1234;
        $command = SendEcmtAppSubmittedCmd::create(['id' => $permitAppId]);

        $organisation = m::mock(Organisation::class);
        $organisation->shouldReceive('getAdminEmailAddresses')->once()->withNoArgs()->andReturn([]);

        $applicationEntity = m::mock(EcmtPermitApplication::class);
        $applicationEntity->shouldReceive('getCreatedBy')->once()->withNoArgs()->andReturn(null);
        $applicationEntity->shouldReceive('getId')->once()->withNoArgs()->andReturn($permitAppId);
        $applicationEntity->shouldReceive('getLicence->getOrganisation')
            ->once()
            ->withNoArgs()
            ->andReturn($organisation);

        $this->repoMap['EcmtPermitApplication']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->once()
            ->andReturn($applicationEntity);

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['ecmtPermitApplication' => $permitAppId], $result->getIds());
        $this->assertSame([MissingEmailException::MSG_NO_ORG_EMAIL], $result->getMessages());
        $this->assertNull($this->sut->getMessage());
    }
}
