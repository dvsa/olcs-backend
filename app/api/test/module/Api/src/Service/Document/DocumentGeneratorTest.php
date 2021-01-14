<?php

namespace Dvsa\OlcsTest\Api\Service\Document;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\ApplicationBundle;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle;
use Dvsa\Olcs\Api\Service\Document\DocumentGenerator;
use Dvsa\Olcs\Api\Service\Document\NamingService;
use Dvsa\Olcs\DocumentShare\Data\Object\File as DsFile;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Dvsa\Olcs\Api\Service\Document\DocumentGenerator
 */
class DocumentGeneratorTest extends MockeryTestCase
{
    /** @var  DocumentGenerator */
    protected $sut;

    /** @var  m\MockInterface */
    protected $contentStore;
    /** @var  m\MockInterface */
    protected $document;
    /** @var  m\MockInterface */
    protected $queryHandlerManager;
    /** @var  m\MockInterface */
    protected $fileUploader;
    /** @var  m\MockInterface */
    protected $namingService;
    /** @var  m\MockInterface */
    protected $documentRepo;

    public function setUp(): void
    {
        $this->sut = new DocumentGenerator();

        $this->contentStore = m::mock();
        $this->document = m::mock();
        $this->queryHandlerManager = m::mock();
        $this->fileUploader = m::mock();
        $this->namingService = m::mock(NamingService::class);
        $this->documentRepo = m::mock();

        /** @var \Laminas\ServiceManager\ServiceLocatorInterface $sm */
        $sm = m::mock(\Laminas\ServiceManager\ServiceLocatorInterface::class)
            ->shouldReceive('get')->with('ContentStore')->andReturn($this->contentStore)
            ->shouldReceive('get')->with('Document')->andReturn($this->document)
            ->shouldReceive('get')->with('QueryHandlerManager')->andReturn($this->queryHandlerManager)
            ->shouldReceive('get')->with('FileUploader')->andReturn($this->fileUploader)
            ->shouldReceive('get')->with('DocumentNamingService')->andReturn($this->namingService)
            ->shouldReceive('get')->with('RepositoryServiceManager')->andReturn(
                m::mock()->shouldReceive('get')->with('Document')->andReturn($this->documentRepo)->getMock()
            )
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

    public function testGenerateFromTemplateWithQueryThrowException()
    {
        $this->expectException(\Exception::class);

        $this->contentStore->shouldReceive('read')
            ->with('x')
            ->andReturn(null)
            ->shouldReceive('read')
            ->with('/templates/x.rtf')
            ->andReturn('file');

        $this->document->shouldReceive('getBookmarkQueries')
            ->with('file', ['y' => 1])
            ->andThrow(\Exception::class)
            ->once();

        $this->sut->generateFromTemplate('x', ['y' => 1], ['z' => 2]);
    }

    public function testGenerateFromTemplateWithQueryFailedQuery()
    {
        $this->expectException('\Exception');

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
            ->andThrow('\Exception');

        $this->sut->generateFromTemplate('x', ['y' => 1], ['z' => 2]);
    }

    public function testGenerateFromTemplateWithQueryWithLicence()
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
            ->andReturn(null)
            ->shouldReceive('read')
            ->with('/templates/GB/x.rtf')
            ->andReturn('file');

        $this->document->shouldReceive('getBookmarkQueries')
            ->with('file', ['y' => 1, 'licence' => 111])
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
            ->andReturn(['b' => 2])
            ->shouldReceive('handleQuery')
            ->with(m::type(LicenceBundle::class))
            ->andReturn(['niFlag' => 'N']);

        $this->sut->generateFromTemplate('x', ['y' => 1, 'licence' => 111], ['z' => 2]);
    }

    public function testGenerateFromTemplateWithQueryWithApplication()
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
            ->andReturn(null)
            ->shouldReceive('read')
            ->with('/templates/NI/x.rtf')
            ->andReturn('file');

        $this->document->shouldReceive('getBookmarkQueries')
            ->with('file', ['y' => 1, 'application' => 111])
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
            ->andReturn(['b' => 2])
            ->shouldReceive('handleQuery')
            ->with(m::type(ApplicationBundle::class))
            ->andReturn(['niFlag' => 'Y']);

        $this->sut->generateFromTemplate('x', ['y' => 1, 'application' => 111], ['z' => 2]);
    }

    public function testGenerateFromTemplateWithQueryWithApplicationWithoutTemplate()
    {
        $this->expectException('\Exception');

        $this->contentStore
            ->shouldReceive('read')->with('x')->andReturn(null)
            ->shouldReceive('read')->with('/templates/x.rtf')->andReturn(null)
            ->shouldReceive('read')->with('/templates/NI/x.rtf')->andReturn(null);

        $this->queryHandlerManager->shouldReceive('handleQuery')
            ->with(m::type(ApplicationBundle::class))
            ->andReturn(['niFlag' => 'Y']);

        $this->sut->generateFromTemplate('x', ['y' => 1, 'application' => 111], ['z' => 2]);
    }

    public function testUploadGeneratedContent()
    {
        $expectFileName = 'fileName';
        $expectBody = 'expect_Body';

        $this->fileUploader
            ->shouldReceive('upload')
            ->andReturnUsing(
                function ($fileName, DsFile $file) use ($expectFileName, $expectBody) {
                    static::assertSame($expectFileName, $fileName);
                    static::assertEquals($expectBody, $file->getContent());

                    return 'EXPECT';
                }
            );

        static::assertEquals('EXPECT', $this->sut->uploadGeneratedContent($expectBody, $expectFileName));
    }

    public function testUploadGeneratedContentError()
    {
        $this->expectException(\Exception::class, 'any error');

        $this->fileUploader
            ->shouldReceive('upload')
            ->andThrow(new \Exception('any error'));

        $this->sut->uploadGeneratedContent('fileName', 'body');
    }

    public function testGenerateFromTemplateWithDocumentId()
    {
        $document = m::mock();
        $document->shouldReceive('getIdentifier')->with()->once()->andReturn('IDENTIFIER');

        $this->documentRepo->shouldReceive('fetchById')->with(412)->once()->andReturn($document);

        $this->contentStore->shouldReceive('read')
            ->with('IDENTIFIER')
            ->andReturn('TEMPLATE');

        $this->document->shouldReceive('getBookmarkQueries')
            ->with('TEMPLATE', [])
            ->andReturn([]);
        $this->document->shouldReceive('populateBookmarks')
            ->with('TEMPLATE', [])
            ->andReturn([]);

        $this->sut->generateFromTemplate(412, [], []);
    }

    public function testGenerateFromTemplateWithDocumentIdNotFound()
    {
        $this->expectException('\Exception', 'Template not found');

        $this->documentRepo->shouldReceive('fetchById')->with(412)->once()
            ->andThrow(\Dvsa\Olcs\Api\Domain\Exception\NotFoundException::class);

        $this->sut->generateFromTemplate(412, [], []);
    }

    public function testDisableBookmarksFlagWithY()
    {
        $this->contentStore->shouldReceive('read')
            ->with('myTemplate')
            ->andReturn(null)
            ->shouldReceive('read')
            ->with('/templates/myTemplate.rtf')
            ->andReturn('file');

        $this->document->shouldNotReceive('getBookmarkQueries');

        $this->queryHandlerManager->shouldNotReceive('handleQuery');

        $this->document->shouldReceive('populateBookmarks')
            ->with('file', ['knownValues' => 2]);

        $this->sut->generateFromTemplate(
            'myTemplate',
            ['queryData' => 1],
            ['knownValues' => 2],
            true
        );

        $expectFileName = 'myTemplate';
        $expectBody = 'expect_Body';

        $this->fileUploader
            ->shouldReceive('upload')
            ->andReturnUsing(
                function ($fileName, DsFile $file) use ($expectFileName, $expectBody) {
                    static::assertSame($expectFileName, $fileName);
                    static::assertEquals($expectBody, $file->getContent());

                    return 'EXPECT';
                }
            );

        static::assertEquals('EXPECT', $this->sut->uploadGeneratedContent($expectBody, $expectFileName));
    }

    public function testDisableBookmarksFlagWithN()
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

        $this->sut->generateFromTemplate('x', ['y' => 1], ['z' => 2], false);

        $expectFileName = 'myTemplate';
        $expectBody = 'expect_Body';

        $this->fileUploader
            ->shouldReceive('upload')
            ->andReturnUsing(
                function ($fileName, DsFile $file) use ($expectFileName, $expectBody) {
                    static::assertSame($expectFileName, $fileName);
                    static::assertEquals($expectBody, $file->getContent());

                    return 'EXPECT';
                }
            );

        static::assertEquals('EXPECT', $this->sut->uploadGeneratedContent($expectBody, $expectFileName));
    }
}
