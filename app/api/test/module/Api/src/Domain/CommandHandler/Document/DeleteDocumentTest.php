<?php

/**
 * Delete Document Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Repository\Document;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Document\DeleteDocument;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Document\DeleteDocument as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\DeleteSubmission as DeleteSubmissionCmd;
use Dvsa\Olcs\Api\Entity\Doc\Document as Entity;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Delete Document Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DeleteDocumentTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeleteDocument();
        $this->mockRepo('Document', Document::class);

        $this->mockedSmServices = [
            'FileUploader' => m::mock()
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [];

        parent::initReferences();
    }

    /**
     * Tests handleCommand
     */
    public function testHandleCommand()
    {
        $command = Cmd::create(['id' => 123]);

        /** @var Entity $document */
        $document = m::mock(Entity::class)->makePartial();
        $document->setIdentifier('ABC');
        $document->shouldReceive('getEbsrSubmissions->isEmpty')->once()->andReturn(true);

        $this->mockedSmServices['FileUploader']->shouldReceive('remove')
            ->once()
            ->with('ABC');

        $this->repoMap['Document']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($document)
            ->shouldReceive('delete')
            ->with($document);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'File removed',
                'Document deleted'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests handleCommand calls the extra side effect if the document is ebsr pack
     */
    public function testHandleCommandEbsrDoc()
    {
        $ebsrSubId = 123345;
        $command = Cmd::create(['id' => 123]);

        /** @var Entity $document */
        $document = m::mock(Entity::class)->makePartial();
        $document->setIdentifier('ABC');
        $document->shouldReceive('getEbsrSubmissions->isEmpty')->once()->andReturn(false);
        $document->shouldReceive('getEbsrSubmissions->first->getId')->once()->andReturn($ebsrSubId);

        $this->mockedSmServices['FileUploader']->shouldReceive('remove')
            ->once()
            ->with('ABC');

        $this->repoMap['Document']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($document)
            ->shouldReceive('delete')
            ->with($document);

        $this->expectedSideEffect(DeleteSubmissionCmd::class, ['id' => $ebsrSubId], new Result());

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'File removed',
                'Document deleted'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
