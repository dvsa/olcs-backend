<?php

/**
 * Send Continuation Not Sought email test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Email\SendContinuationNotSought as Command;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendContinuationNotSought as Sut;
use Dvsa\Olcs\Api\Domain\Repository\SystemParameter as Repo;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;
use Dvsa\Olcs\Email\Domain\Command\SendEmail;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Email\Data\Message;

/**
 * Send Continuation Not Sought email test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class SendContinuationNotSoughtTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new Sut();
        $this->mockRepo('SystemParameter', Repo::class);

        $this->mockedSmServices = [
            TemplateRenderer::class => m::mock(TemplateRenderer::class),
            'translator' => m::mock(\Zend\I18n\Translator\TranslatorInterface::class),
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $licences = [];

        $dtoData = [
            'licences' => $licences,
            'date' => new \DateTime('2015-09-10'),
        ];

        $command = Command::create($dtoData);

        $emailList = 'cns@example.com';

        $this->repoMap['SystemParameter']
            ->shouldReceive('fetchValue')
            ->with(SystemParameter::CNS_EMAIL_LIST)
            ->once()
            ->andReturn($emailList);

        $this->mockedSmServices[TemplateRenderer::class]
            ->shouldReceive('renderBody')
            ->with(
                m::type(Message::class),
                'continuation-not-sought',
                [
                    'licences' => $licences,
                    'startDate' => '10/08/2015', // 1 month prior to endDate
                    'endDate' => '10/09/2015',
                ],
                'default' // layout
            )
            ->once();

        $this->mockedSmServices['translator']
            ->shouldReceive('translate')
            ->with('email.cns.subject')
            ->andReturn('CNS EMAIL FROM %s TO %s');

        $result = new Result();
        $data = [
            'to' => 'cns@example.com',
            'locale' => 'en_GB',
            'subject' => 'CNS EMAIL FROM %s TO %s',
            'subjectVariables' => ['10/08/2015', '10/09/2015']
        ];

        $this->expectedSideEffect(SendEmail::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(['Continuation Not Sought email sent'], $result->getMessages());
    }
}
