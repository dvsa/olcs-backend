<?php

/**
 * SendTmApplicationTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendTmApplication as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Email\SendTmApplication as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Dvsa\Olcs\Email\Service\Client;
use Mockery as m;

/**
 * SendTmApplicationTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class SendTmApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('TransportManagerApplication', Fee::class);

        $this->mockedSmServices = [
            TemplateRenderer::class => m::mock(TemplateRenderer::class),
            Client::class => m::mock(Client::class),
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        parent::initReferences();
    }


    public function dataProviderTestHandleCommand()
    {
        return [
            [0, 'application'],
            [1, 'variation']
        ];
    }

    /**
     * @dataProvider dataProviderTestHandleCommand
     */
    public function testHandleCommand($isVariation, $uriPart)
    {
        $command = Command::create(['id' => 863]);

        $person = new \Dvsa\Olcs\Api\Entity\Person\Person();
        $person->setForename('FORENAME');
        $hcd = new \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails(m::mock(RefData::class));
        $hcd->setEmailAddress('EMAIL');
        $hcd->setPerson($person);
        $tm = new \Dvsa\Olcs\Api\Entity\Tm\TransportManager();
        $tm->setHomeCd($hcd);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setName('ORGANISATION');
        $licence = new \Dvsa\Olcs\Api\Entity\Licence\Licence($organisation, m::mock(RefData::class));
        $licence->setLicNo('LIC01');
        $licence->setTranslateToWelsh('N');
        $application = new \Dvsa\Olcs\Api\Entity\Application\Application(
            $licence,
            m::mock(RefData::class),
            $isVariation
        );
        $application->setId(442);
        $tma = new \Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication();
        $tma->setTransportManager($tm);
        $tma->setApplication($application);
        $tma->setId(75);

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchUsingId')->with($command)->once()
            ->andReturn($tma);

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->with(
            m::type(\Dvsa\Olcs\Email\Data\Message::class),
            'transport-manager-complete-digital-form',
            [
                'name' => 'FORENAME',
                'organisation' => 'ORGANISATION',
                'reference' => 'LIC01/442',
                'signInLink' => 'http://selfserve/'. $uriPart .'/442/transport-managers/details/75/edit-details/'
            ]
        );

        $this->mockedSmServices[Client::class]->shouldReceive('sendEmail')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Email\Data\Message $message) {
                $this->assertSame('EMAIL', $message->getTo());
                $this->assertSame('email.transport-manager-complete-digital-form.subject', $message->getSubject());
                $this->assertSame('en_GB', $message->getLocale());
            }
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['transportManagerApplication' => 75], $result->getIds());
        $this->assertSame(['Transport Manager Application email sent'], $result->getMessages());
    }
}
