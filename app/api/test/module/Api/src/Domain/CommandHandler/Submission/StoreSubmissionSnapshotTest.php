<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Submission;

use Dvsa\Olcs\Api\Domain\CommandHandler\Submission\StoreSubmissionSnapshot;
use Dvsa\Olcs\Transfer\Command\Submission\StoreSubmissionSnapshot as Cmd;
use Dvsa\Olcs\Api\Entity\Submission\Submission;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\Document\Upload;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * StoreSubmissionSnapshotTest
 */
class StoreSubmissionSnapshotTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new StoreSubmissionSnapshot();
        $this->mockRepo('Submission', Repository\Submission::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 15,
            'html' => 'HTML',
        ];
        $command = Cmd::create($data);

        /** @var Submission $savedSubmission */
        $submission = m::mock(Submission::class)->makePartial();
        $submission->setId(15);

        $submission->shouldReceive('getSubmissionType->getDescription')->andReturn('DESC');
        $submission->shouldReceive('getCase->getId')->andReturn(121);
        $submission->shouldReceive('getCase->getLicence->getLicNo')->andReturn('AB123456')->getMock();

        $this->repoMap['Submission']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($submission);

        $createTaskResult = new Result();
        $createTaskResult->addId('task', 1);
        $createTaskResult->addMessage('Task created successfully');

        $params = [
            'content' => base64_encode(trim('HTML')),
            'case' => 121,
            'category' => Category::CATEGORY_SUBMISSION,
            'subCategory' => Category::SUBMISSION_SUB_CATEGORY_OTHER,
            'isExternal' => false,
            'isScan' => false,
            'filename' => 'DESC - Submission - 15 - Case 121 - AB123456.html',
            'description' => 'DESC - Submission - 15 - Case 121 - AB123456',
        ];
        $this->expectedSideEffect(Upload::class, $params, new Result());

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'submission' => 15,
            ],
            'messages' => [
                'Submission snapshot created',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
