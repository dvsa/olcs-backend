<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\BulkSend\ProcessEmail as SendEmailCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\BulkSend\ProcessEmail as ProcessEmailHandler;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Email\Data\Message;
use Dvsa\Olcs\Email\Domain\Command\SendEmail;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Test Bulk Send email
 */
class ProcessEmailTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new ProcessEmailHandler();

        $this->mockRepo('Licence', LicenceRepo::class);

        $this->mockedSmServices = [
            TemplateRenderer::class => m::mock(TemplateRenderer::class),
            'translator' => m::mock(Translator::class),
        ];

        parent::setUp();
    }

    /**
     * test handle command
     *
     * @dataProvider dpTestHandleCommand
     */
    public function testHandleCommand($templateName, $subject)
    {
        $templateVars = [
            'licenceType' => 'Restricted',
            'goodsOrPsv' => 'Goods',
        ];

        $licenceEntity = m::mock(Licence::class);
        $organisation = m::mock(Organisation::class);

        $command = SendEmailCmd::create(['id' => 7, 'templateName' => $templateName]);

        $createUser = m::mock(User::class);
        $createUser->shouldReceive('isInternal')
            ->once()
            ->withNoArgs()
            ->andReturnTrue();

        $licenceEntity->shouldReceive('getId')
            ->once()
            ->withNoArgs()
            ->andReturn(7);

        $licenceEntity->shouldReceive('getCreatedBy')
            ->once()
            ->withNoArgs()
            ->andReturn($createUser);

        $licenceEntity->shouldReceive('getOrganisation')
            ->once()
            ->withNoArgs()
            ->andReturn($organisation);

        $licenceEntity->shouldReceive('getLicenceType->getDescription')
            ->once()
            ->withNoArgs()
            ->andReturn('Restricted');

        $licenceEntity->shouldReceive('getGoodsOrPsv->getDescription')
            ->once()
            ->withNoArgs()
            ->andReturn('Goods');

        $licenceEntity->shouldReceive('getTranslateToWelsh')->andReturn(false);

        $this->repoMap['Licence']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->once()
            ->andReturn($licenceEntity);

        $organisation->shouldReceive('getAdminEmailAddresses')->once()->andReturn(['email@org.com']);

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->once()->with(
            m::type(Message::class),
            $templateName,
            $templateVars,
            'default'
        );

        $expectedData = [
            'to' => 'email@org.com',
            'locale' => 'en_GB',
            'subject' => $subject,
        ];

        $this->expectedSideEffect(SendEmail::class, $expectedData, new Result());

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['Email sent'], $result->getMessages());

        /** @var Message $message */
        $message = $this->sut->getMessage();
        $this->assertSame('email@org.com', $message->getTo());
        $this->assertSame([], $message->getCc());
        $this->assertSame($subject, $message->getSubject());
    }

    public function dpTestHandleCommand()
    {
        return [
            ['unknown-template', 'Important information about your vehicle operator licence'],
            ['report-gv-r', 'Important information about your goods vehicle licence'],
            ['report-psv-r', 'Important information about your PSV licence']
        ];
    }
}
