<?php

/**
 * Send Ebsr Errors Email Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Email\Domain\Command\SendEmail;
use Dvsa\Olcs\Api\Domain\Repository\EbsrSubmission as EbsrSubmissionRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEbsrAbstract;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEbsrErrors;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrErrors as Cmd;

/**
 * Send Ebsr Errors Test
 *
 * Ebsr Error emails follow a different code path if there is no bus reg created. This class tests this behaviour.
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 * @group ebsrEmails
 */
class SendEbsrErrorsTest extends CommandHandlerTestCase
{
    protected $template = ['ebsr-data-error-start', 'ebsr-data-error-list', 'ebsr-data-error-end'];

    /**
     * @var CommandInterface
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new SendEbsrErrors();
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
     * @param string $adminEmail
     * @param string $expectedToAddress
     * @param array|string $rawData
     * @param array $templateVars
     * @param string $subject
     */
    public function testHandleCommand($orgEmail, $adminEmail, $expectedToAddress, $rawData, $templateVars, $subject)
    {
        $ebsrSubmissionId = 1234;
        $orgAdminEmails = [0 => $adminEmail];
        $errors = ['errors'];
        $submissionResultArray = [
            'errors' => $errors,
            'raw_data' => $rawData
        ];

        $submissionResult = serialize($submissionResultArray);

        $submittedDate = '2015-01-15';
        $formattedSubmittedDate = date(SendEbsrAbstract::DATE_FORMAT, strtotime($submittedDate));

        $baseTemplateVars = [
            'submissionDate' => $formattedSubmittedDate,
            'submissionErrors' => $errors,
        ];

        $expectedTemplateVars = array_merge($baseTemplateVars, $templateVars);

        $command = Cmd::create(['id' => $ebsrSubmissionId]);

        $busRegEntity = null; //we're testing what happens when we don't have a bus reg

        $ebsrSubmissionEntity = m::mock(EbsrSubmissionEntity::class);
        $ebsrSubmissionEntity->shouldReceive('getId')->andReturn($ebsrSubmissionId);
        $ebsrSubmissionEntity->shouldReceive('getSubmittedDate')->andReturn($submittedDate);
        $ebsrSubmissionEntity->shouldReceive('getOrganisationEmailAddress')->once()->andReturn($orgEmail);
        $ebsrSubmissionEntity->shouldReceive('getBusReg')->once()->andReturn($busRegEntity);
        $ebsrSubmissionEntity->shouldReceive('getOrganisation->getAdminEmailAddresses')->andReturn($orgAdminEmails);
        $ebsrSubmissionEntity->shouldReceive('getEbsrSubmissionResult')->andReturn($submissionResult);

        $this->repoMap['EbsrSubmission']
            ->shouldReceive('fetchUsingId')
            ->with(m::type(CommandInterface::class), Query::HYDRATE_OBJECT, null)
            ->once()
            ->andReturn($ebsrSubmissionEntity);

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->with(
            m::type(\Dvsa\Olcs\Email\Data\Message::class),
            $this->template,
            $expectedTemplateVars,
            'default'
        );

        $result = new Result();
        $data = [
            'to' => $expectedToAddress,
            'locale' => 'en_GB',
            'subject' => $subject
        ];

        $this->expectedSideEffect(SendEmail::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['ebsrSubmission' => $ebsrSubmissionId], $result->getIds());
        $this->assertSame(['Email sent'], $result->getMessages());
    }

    /**
     * Data provider for handleCommand
     *
     * @return array
     */
    public function handleCommandProvider()
    {
        $licNo = 12345;
        $routeNo = 6789;
        $regNo = $licNo . '/' . $routeNo;
        $startPoint = 'start point';
        $finishPoint = 'finish point';
        $startDate = '2015-12-25';
        $serviceNo = 888;
        $serviceNo2 = 'X99';
        $serviceNo3 = 'Y66';
        $otherServiceNo = [
            0 => $serviceNo2,
            1 => $serviceNo3
        ];

        $formattedServiceNo = $serviceNo . '(' .$serviceNo2 . ',' . $serviceNo3 . ')';

        $formatStartDate = new \DateTime($startDate);
        $formattedStartDate = $formatStartDate->format(SendEbsrErrors::DATE_FORMAT);

        $rawData = [
            'licNo' => $licNo,
            'routeNo' => $routeNo,
            'startPoint' => $startPoint,
            'finishPoint' => $finishPoint,
            'effectiveDate' => $startDate,
            'serviceNo' => $serviceNo,
            'otherServiceNumbers' => $otherServiceNo
        ];

        $rawDataRegNo = ['licNo' => $licNo, 'routeNo' => $routeNo];
        $rawDataStartPoint = ['startPoint' => $startPoint];
        $rawDataFinishPoint = ['finishPoint' => $finishPoint];
        $rawDataEffectiveDate = ['effectiveDate' => $startDate];
        $rawDataServiceNo = ['serviceNo' => $serviceNo, 'otherServiceNumbers' => $otherServiceNo];

        $fullEmailData = [
            'registrationNumber' => $regNo,
            'origin' => $startPoint,
            'destination' => $finishPoint,
            'lineName' => $formattedServiceNo,
            'startDate' => $formattedStartDate,
        ];

        //build expected email data
        $emailDataWithFullBusReg = $this->expectedEmailData($fullEmailData, true);
        $blankEmailData = $this->expectedEmailData([], false);
        $regNoEmailData = $this->expectedEmailData(['registrationNumber' => $regNo], true);
        $originEmailData = $this->expectedEmailData(['origin' => $startPoint], true);
        $destinationEmailData = $this->expectedEmailData(['destination' => $finishPoint], true);
        $lineNameEmailData = $this->expectedEmailData(['lineName' => $formattedServiceNo], true);
        $startDateEmailData = $this->expectedEmailData(['startDate' => $formattedStartDate], true);

        $subjectBus = 'email.ebsr-failed.subject';
        $subjectNoBus = 'email.ebsr-failed-no-bus-reg.subject';

        return [
            ['test@test.com', 'foo@bar.com', 'test@test.com', [], $blankEmailData, $subjectNoBus],
            ['',  'foo@bar.com', 'foo@bar.com', 'not an array', $blankEmailData, $subjectNoBus],
            ['',  'foo@bar.com', 'foo@bar.com', [], $blankEmailData, $subjectNoBus],
            ['test@test.com', 'foo@bar.com', 'test@test.com', $rawData, $emailDataWithFullBusReg, $subjectBus],
            ['',  'foo@bar.com', 'foo@bar.com', $rawData, $emailDataWithFullBusReg, $subjectBus],
            ['',  'foo@bar.com', 'foo@bar.com', $rawDataRegNo, $regNoEmailData, $subjectBus],
            ['',  'foo@bar.com', 'foo@bar.com', $rawDataStartPoint, $originEmailData, $subjectNoBus],
            ['',  'foo@bar.com', 'foo@bar.com', $rawDataFinishPoint, $destinationEmailData, $subjectNoBus],
            ['',  'foo@bar.com', 'foo@bar.com', $rawDataServiceNo, $lineNameEmailData, $subjectNoBus],
            ['',  'foo@bar.com', 'foo@bar.com', $rawDataEffectiveDate, $startDateEmailData, $subjectNoBus],
        ];
    }

    /**
     * Builds an array of expected email data
     *
     * @param array $fields
     * @param bool $hasBusData
     * @return array
     */
    private function expectedEmailData($fields, $hasBusData)
    {
        $data = [
            'registrationNumber' => SendEbsrErrors::UNKNOWN_REG_NO,
            'origin' => SendEbsrErrors::UNKNOWN_START_POINT,
            'destination' => SendEbsrErrors::UNKNOWN_FINISH_POINT,
            'lineName' => SendEbsrErrors::UNKNOWN_SERVICE_NO,
            'startDate' => SendEbsrErrors::UNKNOWN_START_DATE,
            'hasBusData' => $hasBusData
        ];

        foreach ($fields as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
