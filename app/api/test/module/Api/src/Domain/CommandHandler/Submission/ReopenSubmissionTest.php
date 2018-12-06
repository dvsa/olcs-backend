<?php

/**
 * Reopen Submission Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Submission;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Submission\ReopenSubmission;
use Dvsa\Olcs\Api\Domain\Repository\Submission as SubmissionRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Submission\ReopenSubmission as Cmd;
use Dvsa\Olcs\Api\Entity\Submission\Submission as SubmissionEntity;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;

/**
 * Reopen Submission Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class ReopenSubmissionTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ReopenSubmission();
        $this->mockRepo('Submission', SubmissionRepo::class);

        parent::setUp();
    }

    public function testHandleCommandForClosedSubmission()
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
        $this->assertContains('Submission reopened', $result->getMessages());
        $this->assertNull($savedSubmission->getClosedDate());

    }

    /**
     * An open submission should not be re-openable, hence test for exception
     */
    public function testHandleCommandForOpenSubmission()
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

        $this->setExpectedException(ForbiddenException::class);

        $this->sut->handleCommand($command);
    }
}
