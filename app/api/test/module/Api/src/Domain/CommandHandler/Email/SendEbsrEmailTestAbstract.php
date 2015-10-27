<?php

/**
 * Send Ebsr Cancelled Email Test
 *
 * @author Craig R <uk@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEbsrCancelled as CommandHandler;
use Dvsa\Olcs\Email\Domain\Command\SendEmail;
use Dvsa\Olcs\Transfer\Command\Ebsr\SubmissionCreate as SubmissionCreateCommand;
use Dvsa\Olcs\Api\Domain\Repository\EbsrSubmission as EbsrSubmissionRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Query\Ebsr\EbsrSubmission as EbsrSubmissionQuery;

/**
 * Send Ebsr Cancelled Email Test
 *
 * @author Craig R <uk@valtech.co.uk>
 */
abstract class SendEbsrEmailTestAbstract extends CommandHandlerTestCase
{
    protected $template = null;
    protected $sutClass = null;

    /**
     * @var CommandHandler
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new $this->sutClass();
        $this->mockRepo('EbsrSubmission', EbsrSubmissionRepo::class);

        $this->mockedSmServices = [
            TemplateRenderer::class => m::mock(TemplateRenderer::class),
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $ebsrSubmissionId = 1234;

        $la = [
            ['description' => 'LA1'],
            ['description' => 'LA2'],
        ];

        $command = SubmissionCreateCommand::create(['id' => $ebsrSubmissionId]);

        $ebsrSubmissionEntity = m::mock(EbsrSubmissionEntity::class);
        // Makes get busreg return this, so testing is easier.
        $ebsrSubmissionEntity->shouldReceive('getId')->andReturn($ebsrSubmissionId);
        $ebsrSubmissionEntity->shouldReceive('getBusReg')->andReturnSelf();
        $ebsrSubmissionEntity->shouldReceive('getSubmittedDate')->andReturn('2015-01-15');
        $ebsrSubmissionEntity->shouldReceive('getRegistrationNo')->andReturn('1234');
        $ebsrSubmissionEntity->shouldReceive('getStartPoint')->andReturn('Starting');
        $ebsrSubmissionEntity->shouldReceive('getFinishPoint')->andReturn('Finish');
        $ebsrSubmissionEntity->shouldReceive('getServiceNo')->andReturn('Service No');
        $ebsrSubmissionEntity->shouldReceive('getProcessStart')->andReturn('2015-01-16');
        $ebsrSubmissionEntity->shouldReceive('getLicence')->andReturnSelf();
        $ebsrSubmissionEntity->shouldReceive('getTranslateToWelsh')->andReturn(false);
        $ebsrSubmissionEntity->shouldReceive('getOrganisationEmailAddress')->andReturn('EMAIL');
        $ebsrSubmissionEntity->shouldReceive('getLocalAuthoritys')->andReturn($la);

        $this->repoMap['EbsrSubmission']
            ->shouldReceive('fetchUsingId')
            ->with(m::type(EbsrSubmissionQuery::class), Query::HYDRATE_OBJECT, null)
            ->once()
            ->andReturn($ebsrSubmissionEntity);

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->with(
            m::type(\Dvsa\Olcs\Email\Data\Message::class),
            $this->template,
            [
                'submissionDate'     => '15/01/2015',
                'registrationNumber' => '1234',
                'origin' => 'Starting',
                'destination' => 'Finish',
                'lineName' => 'Service No',
                'startDate' => '16/01/2015',
                'localAuthoritys' => 'LA1, LA2',
                'publicationId' => '[PUBLICATION_ID]',
            ],
            null
        );

        $result = new Result();
        $data = [
            'to' => 'EMAIL',
            'locale' => 'en_GB',
            'subject' => 'email.' . $this->template . '.subject'
        ];

        $this->expectedSideEffect(SendEmail::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['ebsrSubmission' => $ebsrSubmissionId], $result->getIds());
        $this->assertSame(['Email sent'], $result->getMessages());
    }
}
