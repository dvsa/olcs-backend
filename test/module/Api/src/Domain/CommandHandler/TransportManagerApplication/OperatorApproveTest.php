<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication\OperatorApprove as CommandHandler;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Email\Domain\Command\SendEmail;
use Dvsa\Olcs\Transfer\Command\TransportManagerApplication\UpdateStatus as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Email\Service\TemplateRenderer;

/**
 * OperatorApproveTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class OperatorApproveTest extends CommandHandlerTestCase
{
    protected $loggedInUser;

    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo(
            'TransportManagerApplication',
            \Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication::class
        );

        $this->mockedSmServices = [
            TemplateRenderer::class => m::mock(TemplateRenderer::class),
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            TransportManagerApplication::STATUS_OPERATOR_SIGNED,
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithVersion()
    {
        $command = Command::create(['id' => 863, 'version' => 234]);

        $tma = m::mock(TransportManagerApplication::class)->makePartial();
        $tma->setId(12);

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchUsingId')->once()
            ->with($command, \Doctrine\ORM\Query::HYDRATE_OBJECT, 234)->andReturn($tma);
        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once()->andReturnUsing(
            function (TransportManagerApplication $tma) {
                $this->assertSame(
                    $this->refData[TransportManagerApplication::STATUS_OPERATOR_SIGNED],
                    $tma->getTmApplicationStatus()
                );
            }
        );

        $this->assertEmailSent($tma);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWithoutVersion()
    {
        $command = Command::create(['id' => 863]);

        $tma = m::mock(TransportManagerApplication::class)->makePartial();
        $tma->setId(12);

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchUsingId')->once()
            ->with($command)->andReturn($tma);
        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once()->andReturnUsing(
            function (TransportManagerApplication $tma) {
                $this->assertSame(
                    $this->refData[TransportManagerApplication::STATUS_OPERATOR_SIGNED],
                    $tma->getTmApplicationStatus()
                );
            }
        );

        $this->assertEmailSent($tma);

        $this->sut->handleCommand($command);
    }

    private function assertEmailSent($tma)
    {
        $tma->shouldReceive('getTransportManager->getHomeCd->getEmailAddress')->with()->once()->andReturn('email1');
        $tma->shouldReceive('getApplication->getLicence->getTranslateToWelsh')->with()->once()->andReturn('Y');
        $tma->shouldReceive('getApplication->getLicence->getOrganisation->getName')->with()->once()
            ->andReturn('ORG_NAME');
        $tma->shouldReceive('getApplication->getLicence->getLicNo')->with()->once()->andReturn('LIC01');
        $tma->shouldReceive('getApplication->getId')->with()->twice()->andReturn(76);

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Email\Data\Message $message, $template, $vars, $layout) {

                $this->assertSame('email.transport-manager-confirmed.subject', $message->getSubject());
                $this->assertSame('email1', $message->getTo());
                $this->assertSame('cy_GB', $message->getLocale());

                $this->assertSame('transport-manager-confirmed', $template);
                $this->assertSame(
                    [
                        'operatorName' => 'ORG_NAME',
                        'licNo' => 'LIC01',
                        'applicationId' => 76,
                        'tmaUrl' => 'http://selfserve/application/76/transport-managers/details/12/'
                    ],
                    $vars
                );
                $this->assertSame('default', $layout);

            }
        );

        $result = new Result();
        $data = [
            'to' => 'email1'
        ];
        $this->expectedSideEffect(SendEmail::class, $data, $result);
    }
}
