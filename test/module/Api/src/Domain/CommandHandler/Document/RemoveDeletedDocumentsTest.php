<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Queue\Create;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Document\RemoveDeletedDocuments;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Document\RemoveDeletedDocuments as Cmd;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * RemoveDeletedDocumentsTest
 */
class RemoveDeletedDocumentsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new RemoveDeletedDocuments();
        $this->mockRepo('DocumentToDelete', Repository\DocumentToDelete::class);
        $this->mockRepo('SystemParameter', Repository\SystemParameter::class);

        $this->mockUploader = m::mock(ContentStoreFileUploader::class);
        $this->mockedSmServices['FileUploader'] = $this->mockUploader;

        parent::setUp();
    }

    public function testHandleCreateService()
    {
        $this->sut->createService($this->commandHandler);
        $this->assertSame($this->mockUploader, $this->sut->getContentStoreService());
    }

    public function testHandleCommand()
    {
        $command = Cmd::create([]);

        $this->mockUploader->shouldReceive('remove')->with('doc1.rtf')->once()->andReturn(
            m::mock(Response::class)->shouldReceive('isOk')->with()->once()->andReturn(true)->getMock()
        );
        $this->mockUploader->shouldReceive('remove')->with('doc2.rtf')->once()->andReturn(
            m::mock(Response::class)
                ->shouldReceive('isOk')->with()->once()->andReturn(false)
                ->shouldReceive('isNotFound')->with()->once()->andReturn(true)
                ->getMock()
        );
        $this->mockUploader->shouldReceive('remove')->with('doc3.rtf')->once()->andReturn(
            m::mock(Response::class)
                ->shouldReceive('isOk')->with()->once()->andReturn(false)
                ->shouldReceive('isNotFound')->with()->once()->andReturn(false)
                ->shouldReceive('getStatusCode')->with()->once()->andReturn(500)
                ->getMock()
        );
        $this->mockUploader->shouldReceive('remove')->with('doc4.rtf')->once()->andReturn(
            m::mock(Response::class)->shouldReceive('isOk')->with()->once()->andReturn(true)->getMock()
        );

        $documentsToDelete = [
            (new \Dvsa\Olcs\Api\Entity\Doc\DocumentToDelete())->setDocumentStoreId('doc4.rtf'),
            (new \Dvsa\Olcs\Api\Entity\Doc\DocumentToDelete())->setDocumentStoreId('doc3.rtf'),
            (new \Dvsa\Olcs\Api\Entity\Doc\DocumentToDelete())->setDocumentStoreId('doc2.rtf'),
            (new \Dvsa\Olcs\Api\Entity\Doc\DocumentToDelete())->setDocumentStoreId('doc1.rtf'),
        ];

        $this->repoMap['SystemParameter']
            ->shouldReceive('getDisableDataRetentionDocumentDelete')->with()->once()->andReturn(false);

        $this->repoMap['DocumentToDelete']
            ->shouldReceive('fetchListOfDocumentToDelete')->with(100)->once()->andReturn($documentsToDelete)
            ->shouldReceive('delete')->with($documentsToDelete[0])->once()
            ->shouldReceive('delete')->with($documentsToDelete[2])->once()
            ->shouldReceive('delete')->with($documentsToDelete[3])->once()
            ->shouldReceive('fetchListOfDocumentToDelete')->with(1)->once()->andReturn([]);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Document delete failed \'doc3.rtf\', code = 500',
                'Remove documents : 2 success, 1 not found, 1 errors',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandQueueItemCreated()
    {
        $command = Cmd::create([]);

        $this->repoMap['SystemParameter']
            ->shouldReceive('getDisableDataRetentionDocumentDelete')->with()->once()->andReturn(false);

        $this->repoMap['DocumentToDelete']
            ->shouldReceive('fetchListOfDocumentToDelete')->with(100)->once()->andReturn([])
            ->shouldReceive('fetchListOfDocumentToDelete')->with(1)->once()->andReturn(['FOO']);

        $this->expectedSideEffect(
            Create::class,
            ['type' => Queue::TYPE_REMOVE_DELETED_DOCUMENTS, 'status' => Queue::STATUS_QUEUED],
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Remove documents : 0 success, 0 not found, 0 errors',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
    public function testHandleCommandDisabled()
    {
        $command = Cmd::create([]);

        $this->repoMap['SystemParameter']
            ->shouldReceive('getDisableDataRetentionDocumentDelete')->with()->once()->andReturn(true);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Removing deleted documents is disabled by system parameter',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
