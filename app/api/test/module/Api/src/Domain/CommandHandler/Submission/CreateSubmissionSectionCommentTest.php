<?php

/**
 * Create SubmissionSectionComment Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Submission;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Submission\CreateSubmissionSectionComment;
use Dvsa\Olcs\Api\Domain\Repository\SubmissionSectionComment;
use Dvsa\Olcs\Api\Entity\Submission\Submission;
use Dvsa\Olcs\Api\Entity\Submission\SubmissionSectionComment as SubmissionSectionCommentEntity;
use Dvsa\Olcs\Transfer\Command\Submission\CreateSubmissionSectionComment as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Create SubmissionSectionComment Test
 */
class CreateSubmissionSectionCommentTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateSubmissionSectionComment();
        $this->mockRepo('SubmissionSectionComment', SubmissionSectionComment::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'case-summary'
        ];

        $this->references = [
            Submission::class => [
                1 => m::mock(Submission::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'submission' => 1,
            'submissionSection' => 'case-summary',
            'comment' => 'testing',
        ];

        $command = Cmd::create($data);

        /** @var SubmissionSectionCommentEntity $savedSubmissionSectionComment */
        $savedSubmissionSectionComment = null;

        $this->repoMap['SubmissionSectionComment']->shouldReceive('save')
            ->once()
            ->with(m::type(SubmissionSectionCommentEntity::class))
            ->andReturnUsing(
                function (
                    SubmissionSectionCommentEntity $submissionSectionComment
                ) use (&$savedSubmissionSectionComment) {
                    $submissionSectionComment->setId(111);
                    $savedSubmissionSectionComment = $submissionSectionComment;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'submissionSectionComment' => 111,
                'submissionSection' => 'case-summary'
            ],
            'messages' => [
                'Submission section comment created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertSame(
            $this->references[Submission::class][$data['submission']],
            $savedSubmissionSectionComment->getSubmission()
        );

        $this->assertEquals($data['comment'], $savedSubmissionSectionComment->getComment());

    }
}
