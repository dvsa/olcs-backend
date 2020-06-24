<?php

/**
 *  Submission InformationComplete Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Submission;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Submission\InformationComplete;
use Dvsa\Olcs\Api\Domain\Repository\Submission as SubmissionRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Submission\InformationCompleteSubmission as Cmd;
use Dvsa\Olcs\Api\Entity\Submission\Submission as SubmissionEntity;
use Dvsa\Olcs\Api\Domain\Command\System\GenerateSlaTargetDate as GenerateSlaTargetDateCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 *  Submission InformationComplete Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class InformationCompleteTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new InformationComplete();
        $this->mockRepo('Submission', SubmissionRepo::class);

        parent::setUp();
    }

    public function testHandleCommandUnassignedSubmission()
    {
        $id = 2;
        $infoCompleteDate = '2015-01-05';
        $command = Cmd::create(
            [
                'id' => $id,
                'version' => 1,
                'informationCompleteDate' => $infoCompleteDate
            ]
        );

        /** @var SubmissionEntity $savedSubmission */
        $submission = m::mock(SubmissionEntity::class)->makePartial();
        $submission->setId($id);

        $this->repoMap['Submission']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($submission);

        $this->repoMap['Submission']->shouldReceive('save')
            ->once()
            ->with(m::type(SubmissionEntity::class))
            ->andReturnUsing(
                function (SubmissionEntity $submission) use (&$savedSubmission) {
                    $savedSubmission = $submission;
                }
            );

        $this->expectedSideEffect(
            GenerateSlaTargetDateCmd::class,
            [
                'submission' => $id
            ],
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('Submission updated successfully', $result->getMessages());
        $this->assertEquals($infoCompleteDate, $savedSubmission->getInformationCompleteDate());
    }
}
