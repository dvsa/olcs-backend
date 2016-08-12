<?php

/**
 * Send Ebsr Cancelled Email Test
 *
 * @author Craig R <uk@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Email\Domain\Command\SendEmail;
use Dvsa\Olcs\Api\Domain\Repository\EbsrSubmission as EbsrSubmissionRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LocalAuthorityEntity;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
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
    protected $cmdClass = null;

    /**
     * @var CommandInterface
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

    /**
     * @dataProvider handleCommandProvider
     *
     * @param string $orgEmail
     */
    public function testHandleCommand($orgEmail, $adminEmail, $expectedToAddress, $cmdClass)
    {
        $ebsrSubmissionId = 1234;
        $regNo = 5678;
        $laDescription1 = 'la description 1';
        $laEmail1 = 'terry.valtech@gmail.com';
        $laEmail2 = 'terry.valtech+1@gmail.com';
        $laDescription2 = 'la description 2';
        $startPoint = 'start point';
        $endPoint = 'end point';
        $serviceNumbers = '99999 (12345,567910)';
        $orgAdminEmails = [0 => $adminEmail];
        $submissionResult = [];

        $submittedDate = '2015-01-15';
        $formattedSubmittedDate = date(SendEbsrAbstract::DATE_FORMAT, strtotime($submittedDate));

        $effectiveDate = new \DateTime('2015-01-16 00:00:00');
        $formattedEffectiveDate = $effectiveDate->format(SendEbsrAbstract::DATE_FORMAT);

        $la1 = m::mock(LocalAuthorityEntity::class)->makePartial();
        $la1->setDescription($laDescription1);
        $la1->setEmailAddress($laEmail1);

        $la2 = m::mock(LocalAuthorityEntity::class)->makePartial();
        $la2->setDescription($laDescription2);
        $la2->setEmailAddress($laEmail2);

        $la = new ArrayCollection([$la1, $la2]);

        $command = $cmdClass::create(['id' => $ebsrSubmissionId]);

        $busRegEntity = m::mock(BusRegEntity::class);
        $busRegEntity->shouldReceive('getRegNo')->times(2)->andReturn($regNo);
        $busRegEntity->shouldReceive('getStartPoint')->once()->andReturn($startPoint);
        $busRegEntity->shouldReceive('getFinishPoint')->once()->andReturn($endPoint);
        $busRegEntity->shouldReceive('getEffectiveDate')->once()->andReturn($effectiveDate);
        $busRegEntity->shouldReceive('getLocalAuthoritys')->times(2)->andReturn($la);
        $busRegEntity->shouldReceive('getFormattedServiceNumbers')->once()->andReturn($serviceNumbers);
        $busRegEntity->shouldReceive('getPublicationSectionForGrantEmail')->never(); //only for registered & cancelled
        $busRegEntity->shouldReceive('getPublicationLinksForGrantEmail')->never(); //only for registered & cancelled

        $ebsrSubmissionEntity = m::mock(EbsrSubmissionEntity::class);
        $ebsrSubmissionEntity->shouldReceive('getId')->andReturn($ebsrSubmissionId);
        $ebsrSubmissionEntity->shouldReceive('getSubmittedDate')->andReturn($submittedDate);
        $ebsrSubmissionEntity->shouldReceive('getOrganisationEmailAddress')->once()->andReturn($orgEmail);
        $ebsrSubmissionEntity->shouldReceive('getBusReg')->once()->andReturn($busRegEntity);
        $ebsrSubmissionEntity->shouldReceive('getOrganisation->getAdminEmailAddresses')->andReturn($orgAdminEmails);
        $ebsrSubmissionEntity->shouldReceive('getDecodedSubmissionResult')->andReturn($submissionResult);

        $this->repoMap['EbsrSubmission']
            ->shouldReceive('fetchUsingId')
            ->with(m::type(CommandInterface::class), Query::HYDRATE_OBJECT, null)
            ->once()
            ->andReturn($ebsrSubmissionEntity);

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->with(
            m::type(\Dvsa\Olcs\Email\Data\Message::class),
            $this->template,
            [
                'submissionDate' => $formattedSubmittedDate,
                'registrationNumber' => $regNo,
                'origin' => $startPoint,
                'destination' => $endPoint,
                'lineName' => $serviceNumbers,
                'startDate' => $formattedEffectiveDate,
                'localAuthoritys' => $laDescription1 . ', ' . $laDescription2,
                'submissionErrors' => $submissionResult,
                'hasBusData' => true,
                'publicationId' => null,
            ],
            'default'
        );

        $result = new Result();
        $data = [
            'to' => $expectedToAddress,
            'locale' => 'en_GB',
            'subject' => 'email.' . $this->template . '.subject'
        ];

        $this->expectedSideEffect(SendEmail::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['ebsrSubmission' => $ebsrSubmissionId], $result->getIds());
        $this->assertSame(['Email sent'], $result->getMessages());
    }

    public function handleCommandProvider()
    {
        return [
            ['test@test.com', 'foo@bar.com', 'test@test.com', $this->cmdClass],
            ['',  'foo@bar.com', 'foo@bar.com', $this->cmdClass]
        ];
    }
}
