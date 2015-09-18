<?php

/**
 * Document Generation Helper Service test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Service\Document;

use Dvsa\Olcs\Api\Service\Document\NamingService;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Service\Document\DocumentGenerator;

/**
 * Document Generation Helper Service test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DocumentGeneratorTest extends MockeryTestCase
{
    protected $sut;

    protected $contentStore;
    protected $document;
    protected $queryHandlerManager;
    protected $fileUploader;
    protected $namingService;

    public function setUp()
    {
        $this->sut = new DocumentGenerator();

        $this->contentStore = m::mock();
        $this->document = m::mock();
        $this->queryHandlerManager = m::mock();
        $this->fileUploader = m::mock();
        $this->namingService = m::mock(NamingService::class);

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('ContentStore')
            ->andReturn($this->contentStore)
            ->shouldReceive('get')
            ->with('Document')
            ->andReturn($this->document)
            ->shouldReceive('get')
            ->with('QueryHandlerManager')
            ->andReturn($this->queryHandlerManager)
            ->shouldReceive('get')
            ->with('FileUploader')
            ->andReturn($this->fileUploader)
            ->shouldReceive('get')
            ->with('DocumentNamingService')
            ->andReturn($this->namingService)
            ->getMock();

        $this->sut->createService($sm);
    }

    public function testGenerateFromTemplateWithEmptyQuery()
    {
        $this->contentStore->shouldReceive('read')
            ->with('x')
            ->andReturn(null)
            ->shouldReceive('read')
            ->with('/templates/x.rtf')
            ->andReturn('file');

        $this->document
                ->shouldReceive('getBookmarkQueries')
                ->with('file', [])
                ->andReturn([])
                ->shouldReceive('populateBookmarks')
                ->with('file', []);

        $this->sut->generateFromTemplate('x');
    }

    public function testGenerateFromTemplateWithQuery()
    {
        $query = [
            'a' => m::mock(QueryInterface::class),
            'b' => [
                m::mock(QueryInterface::class),
                m::mock(QueryInterface::class)
            ]
        ];

        $this->contentStore->shouldReceive('read')
            ->with('x')
            ->andReturn(null)
            ->shouldReceive('read')
            ->with('/templates/x.rtf')
            ->andReturn('file');

        $this->document->shouldReceive('getBookmarkQueries')
            ->with('file', ['y' => 1])
            ->andReturn($query)
            ->shouldReceive('populateBookmarks')
            ->with('file', ['a' => ['a' => 1], 'b' => [['b' => 1], ['b' => 2]], 'z' => 2]);

        $this->queryHandlerManager->shouldReceive('handleQuery')
            ->once()
            ->with($query['a'])
            ->andReturn(['a' => 1])
            ->shouldReceive('handleQuery')
            ->once()
            ->with($query['b'][0])
            ->andReturn(['b' => 1])
            ->shouldReceive('handleQuery')
            ->once()
            ->with($query['b'][1])
            ->andReturn(['b' => 2]);

        $this->sut->generateFromTemplate('x', ['y' => 1], ['z' => 2]);
    }

    public function testUploadGeneratedContent()
    {
        $this->fileUploader->shouldReceive('setFile')
            ->with(['content' => 'foo'])
            ->shouldReceive('upload')
            ->with('docs')
            ->andReturn('result');

        $this->sut->uploadGeneratedContent('foo', 'docs');
    }
}
