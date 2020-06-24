<?php

/**
 * Close Submission Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Submission;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Submission\CloseSubmission;
use Dvsa\Olcs\Api\Domain\Repository\Submission as SubmissionRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Submission\CloseSubmission as Cmd;
use Dvsa\Olcs\Api\Entity\Submission\Submission as SubmissionEntity;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;

/**
 * Close Submission Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class CloseSubmissionTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CloseSubmission();
        $this->mockRepo('Submission', SubmissionRepo::class);

        parent::setUp();
    }

    public function testHandleCommandCloseableSubmission()
    {
        $command = Cmd::create(
            [
                'id' => 2
            ]
        );

        /** @var SubmissionEntity $savedSubmission */
        $submission = m::mock(SubmissionEntity::class)->makePartial();
        $submission->setId(2);

        $this->repoMap['Submission']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($submission);

        $this->repoMap['Submission']->shouldReceive('save')
            ->once()
            ->with(m::type(SubmissionEntity::class))
            ->andReturnUsing(
                function (SubmissionEntity $submission) use (&$savedSubmission) {
                    $savedSubmission = $submission;
                }
            );

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('Submission closed', $result->getMessages());
        $this->assertInstanceOf('DateTime', $savedSubmission->getClosedDate());
    }

    public function testHandleCommandNotCloseableSubmission()
    {
        $command = Cmd::create(
            [
                'id' => 2
            ]
        );

        /** @var SubmissionEntity $savedSubmission */
        $submission = m::mock(SubmissionEntity::class)->makePartial();
        $submission->setId(2);
        $submission->setClosedDate(new \DateTime('now'));

        $this->repoMap['Submission']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($submission);

        $this->expectException(ForbiddenException::class);

        $this->sut->handleCommand($command);
    }
}
