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
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LocalAuthorityEntity;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Query\Ebsr\EbsrSubmission as EbsrSubmissionQuery;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEbsrAbstract;

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
        $regNo = 5678;
        $laDescription1 = 'la description 1';
        $laDescription2 = 'la description 2';
        $startPoint = 'start point';
        $endPoint = 'end point';
        $orgEmail = 'test@testing.com';
        $serviceNo = 99999;

        $submittedDate = '2015-01-15';
        $formattedSubmittedDate = date(SendEbsrAbstract::DATE_FORMAT, strtotime($submittedDate));

        $effectiveDate = new \DateTime('2015-01-16 00:00:00');
        $formattedEffectiveDate = $effectiveDate->format(SendEbsrAbstract::DATE_FORMAT);

        $la1 = m::mock(LocalAuthorityEntity::class)->makePartial();
        $la1->setDescription($laDescription1);

        $la2 = m::mock(LocalAuthorityEntity::class)->makePartial();
        $la2->setDescription($laDescription2);

        $la = new ArrayCollection([$la1, $la2]);

        $command = SubmissionCreateCommand::create(['id' => $ebsrSubmissionId]);

        $ebsrSubmissionEntity = m::mock(EbsrSubmissionEntity::class);
        // Makes get busreg return this, so testing is easier.
        $ebsrSubmissionEntity->shouldReceive('getId')->andReturn($ebsrSubmissionId);
        $ebsrSubmissionEntity->shouldReceive('getSubmittedDate')->andReturn($submittedDate);
        $ebsrSubmissionEntity->shouldReceive('getBusReg->getRegNo')->andReturn($regNo);
        $ebsrSubmissionEntity->shouldReceive('getBusReg->getStartPoint')->andReturn($startPoint);
        $ebsrSubmissionEntity->shouldReceive('getBusReg->getFinishPoint')->andReturn($endPoint);
        $ebsrSubmissionEntity->shouldReceive('getBusReg->getServiceNo')->andReturn($serviceNo);
        $ebsrSubmissionEntity->shouldReceive('getBusReg->getEffectiveDate')->andReturn($effectiveDate);
        $ebsrSubmissionEntity->shouldReceive('getBusReg->getLicence->getTranslateToWelsh')->andReturn(false);
        $ebsrSubmissionEntity->shouldReceive('getOrganisationEmailAddress')->andReturn($orgEmail);
        $ebsrSubmissionEntity->shouldReceive('getBusReg->getLocalAuthoritys')->andReturn($la);

        $this->repoMap['EbsrSubmission']
            ->shouldReceive('fetchUsingId')
            ->with(m::type(EbsrSubmissionQuery::class), Query::HYDRATE_OBJECT, null)
            ->once()
            ->andReturn($ebsrSubmissionEntity);

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->with(
            m::type(\Dvsa\Olcs\Email\Data\Message::class),
            $this->template,
            [
                'submissionDate'     => $formattedSubmittedDate,
                'registrationNumber' => $regNo,
                'origin' => $startPoint,
                'destination' => $endPoint,
                'lineName' => $serviceNo,
                'startDate' => $formattedEffectiveDate,
                'localAuthoritys' => $laDescription1 . ', ' . $laDescription2 . '.',
                'publicationId' => '[PUBLICATION_ID]',
            ],
            null
        );

        $result = new Result();
        $data = [
            'to' => $orgEmail,
            'locale' => 'en_GB',
            'subject' => 'email.' . $this->template . '.subject'
        ];

        $this->expectedSideEffect(SendEmail::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['ebsrSubmission' => $ebsrSubmissionId], $result->getIds());
        $this->assertSame(['Email sent'], $result->getMessages());
    }
}
