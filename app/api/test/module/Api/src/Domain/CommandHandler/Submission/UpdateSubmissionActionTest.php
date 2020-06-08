<?php

/**
 * Update SubmissionAction Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Submission;

use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\Submission\UpdateSubmissionAction;
use Dvsa\Olcs\Api\Domain\Repository\SubmissionAction;
use Dvsa\Olcs\Api\Entity\Submission\SubmissionAction as SubmissionActionEntity;
use Dvsa\Olcs\Api\Entity\Pi\Reason;
use Dvsa\Olcs\Transfer\Command\Submission\UpdateSubmissionAction as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Update SubmissionAction Test
 */
class UpdateSubmissionActionTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateSubmissionAction();
        $this->mockRepo('SubmissionAction', SubmissionAction::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'sub_st_rec_grant_as'
        ];

        $this->references = [
            Reason::class => [
                221 => m::mock(Reason::class)
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 1,
            'version' => 1,
            'actionTypes' => ['sub_st_rec_grant_as'],
            'reasons' => [221],
            'comment' => 'testing',
        ];

        $command = Cmd::create($data);

        /** @var SubmissionActionEntity $savedSubmissionAction */
        $submissionAction = m::mock(SubmissionActionEntity::class)->makePartial();
        $submissionAction->setId(1);

        $this->repoMap['SubmissionAction']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($submissionAction);

        /** @var SubmissionActionEntity $savedSubmissionAction */
        $savedSubmissionAction = null;

        $this->repoMap['SubmissionAction']->shouldReceive('save')
            ->once()
            ->with(m::type(SubmissionActionEntity::class))
            ->andReturnUsing(
                function (SubmissionActionEntity $submissionAction) use (&$savedSubmissionAction) {
                    $savedSubmissionAction = $submissionAction;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'submissionAction' => 1,
            ],
            'messages' => [
                'Submission Action updated successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertSame(
            [$this->refData['sub_st_rec_grant_as']],
            $savedSubmissionAction->getActionTypes()
        );
        $this->assertEquals($data['comment'], $savedSubmissionAction->getComment());
        $this->assertSame(
            $this->references[Reason::class][$data['reasons'][0]],
            $savedSubmissionAction->getReasons()[0]
        );
    }

    public function testHandleInvalidCommand()
    {
        $this->expectException(\Dvsa\Olcs\Api\Domain\Exception\ValidationException::class);

        $data = [
            'id' => 1,
            'version' => 1,
            'actionTypes' => ['sub_st_rec_ptr'],
            'reasons' => [],
            'isDecision' => 'N',
            'comment' => 'testing',
        ];

        $command = Cmd::create($data);

        /** @var SubmissionActionEntity $savedSubmissionAction */
        $submissionAction = m::mock(SubmissionActionEntity::class)->makePartial();
        $submissionAction->setId(1);
        $submissionAction->setIsDecision('N');

        $this->repoMap['SubmissionAction']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($submissionAction);

        $this->sut->handleCommand($command);
    }
}
