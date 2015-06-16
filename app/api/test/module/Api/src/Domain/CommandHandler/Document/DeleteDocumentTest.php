<?php

/**
 * Delete Document Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Repository\Document;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Document\DeleteDocument;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Document\DeleteDocument as Cmd;
use Dvsa\Olcs\Api\Entity\Doc\Document as Entity;
use ZfcRbac\Service\AuthorizationService;

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

    public function testHandleCommand()
    {
        $command = Cmd::create(['id' => 123]);

        /** @var Entity $document */
        $document = m::mock(Entity::class)->makePartial();
        $document->setIdentifier('ABC');

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
}
