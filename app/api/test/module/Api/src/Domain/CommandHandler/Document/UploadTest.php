<?php

/**
 * Upload Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocument;
use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Document\Upload;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository\Document;
use Dvsa\Olcs\Api\Service\Document\NamingService;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Dvsa\Olcs\Api\Service\File\File;
use Dvsa\Olcs\Api\Service\File\MimeNotAllowedException;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity;

/**
 * Upload Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UploadTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Upload();
        $this->mockRepo('Document', Document::class);

        $this->mockedSmServices['FileUploader'] = m::mock(ContentStoreFileUploader::class);
        $this->mockedSmServices['DocumentNamingService'] = m::mock(NamingService::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            Entity\Licence\Licence::class => [
                111 => m::mock(Entity\Licence\Licence::class)
            ],
            Entity\Application\Application::class => [
                222 => m::mock(Entity\Application\Application::class)
            ],
            Entity\Cases\Cases::class => [
                333 => m::mock(Entity\Cases\Cases::class)
            ],
            Entity\Tm\TransportManager::class => [
                444 => m::mock(Entity\Tm\TransportManager::class)
            ],
            Entity\Bus\BusReg::class => [
                555 => m::mock(Entity\Bus\BusReg::class)
            ],
            Entity\Organisation\Organisation::class => [
                666 => m::mock(Entity\Organisation\Organisation::class)
            ]
        ];

        $this->categoryReferences = [
            11 => m::mock(Entity\System\Category::class)
        ];

        $this->subCategoryReferences = [
            22 => m::mock(Entity\System\SubCategory::class)
        ];

        parent::initReferences();
    }

    /**
     * @dataProvider provideLinkedEntity
     */
    public function testHandleCommand($key, $id, $entityClass)
    {
        $data = [
            'content' => base64_encode('<foo>'),
            'filename' => 'foo.pdf',
            'category' => 11,
            'subCategory' => 22,
            'isExternal' => 1,
            'description' => 'foo',
            'user' => 1
        ];

        $data[$key] = $id;

        $command = \Dvsa\Olcs\Transfer\Command\Document\Upload::create($data);

        $this->mockedSmServices['DocumentNamingService']->shouldReceive('generateName')
            ->once()
            ->with(
                'foo',
                'pdf',
                $this->categoryReferences[11],
                $this->subCategoryReferences[22],
                $this->references[$entityClass][$id]
            )
            ->andReturn('/some/identifier.pdf');

        /** @var File $file */
        $file = m::mock(File::class)->makePartial();
        $file->setIdentifier('/some/identifier.pdf');

        $this->mockedSmServices['FileUploader']->shouldReceive('setFile')
            ->once()
            ->with(m::type(File::class))
            ->andReturnUsing(
                function (File $file) {
                    $this->assertEquals('foo.pdf', $file->getName());
                    $this->assertEquals('<foo>', $file->getContent());
                    $this->assertEquals(5, $file->getSize());
                }
            )
            ->shouldReceive('upload')
            ->with('/some/identifier.pdf')
            ->andReturn($file);

        $result = new Result();
        $result->addMessage('CreateDocumentSpecific');
        $data = [
            'identifier' => '/some/identifier.pdf',
            'filename' => '/some/identifier.pdf',
            'description' => 'foo',
            'isExternal' => 1,
            'user' => 1
        ];
        $this->expectedSideEffect(CreateDocumentSpecific::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'identifier' => '/some/identifier.pdf'
            ],
            'messages' => [
                'File uploaded',
                'CreateDocumentSpecific'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests EBSR doc upload throws exception if file isn't zip
     *
     * @dataProvider provideLinkedEntity
     * @param string $key
     * @param int $id
     * @param string $entityClass
     */
    public function testHandleCommandInvalidEbsrMime($key, $id, $entityClass)
    {
        $this->setExpectedException(ValidationException::class);

        $data = [
            'content' => base64_encode('<foo>'),
            'filename' => 'foo.pdf',
            'category' => 11,
            'subCategory' => 22,
            'isExternal' => 1,
            'isEbsrPack' => true,
            'user' => 1
        ];

        $data[$key] = $id;

        $command = \Dvsa\Olcs\Transfer\Command\Document\Upload::create($data);

        $this->mockedSmServices['DocumentNamingService']->shouldReceive('generateName')
            ->once()
            ->with(
                'foo',
                'pdf',
                $this->categoryReferences[11],
                $this->subCategoryReferences[22],
                $this->references[$entityClass][$id]
            )
            ->andReturn('/some/identifier.pdf');

        $this->sut->handleCommand($command);
    }

    /**
     * @dataProvider provideLinkedEntity
     */
    public function testHandleCommandInvalidMime($key, $id, $entityClass)
    {
        $this->setExpectedException(ValidationException::class);

        $data = [
            'content' => base64_encode('<foo>'),
            'filename' => 'foo.pdf',
            'category' => 11,
            'subCategory' => 22,
            'isExternal' => 1,
            'user' => 1
        ];

        $data[$key] = $id;

        $command = \Dvsa\Olcs\Transfer\Command\Document\Upload::create($data);

        $this->mockedSmServices['DocumentNamingService']->shouldReceive('generateName')
            ->once()
            ->with(
                'foo',
                'pdf',
                $this->categoryReferences[11],
                $this->subCategoryReferences[22],
                $this->references[$entityClass][$id]
            )
            ->andReturn('/some/identifier.pdf');

        /** @var File $file */
        $file = m::mock(File::class)->makePartial();
        $file->setIdentifier('/some/identifier.pdf');

        $this->mockedSmServices['FileUploader']->shouldReceive('setFile')
            ->once()
            ->with(m::type(File::class))
            ->andReturnUsing(
                function (File $file) {
                    $this->assertEquals('foo.pdf', $file->getName());
                    $this->assertEquals('<foo>', $file->getContent());
                    $this->assertEquals(5, $file->getSize());
                }
            )
            ->shouldReceive('upload')
            ->with('/some/identifier.pdf')
            ->andThrow(MimeNotAllowedException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWithoutIsExternal()
    {
        $data = [
            'content' => base64_encode('<foo>'),
            'filename' => 'foo.pdf',
            'category' => 11,
            'subCategory' => 22,
            'user' => 1
        ];

        $command = \Dvsa\Olcs\Transfer\Command\Document\Upload::create($data);

        $this->mockedSmServices['DocumentNamingService']->shouldReceive('generateName')
            ->once()
            ->with(
                'foo',
                'pdf',
                $this->categoryReferences[11],
                $this->subCategoryReferences[22],
                null
            )
            ->andReturn('/some/identifier.pdf');

        /** @var File $file */
        $file = m::mock(File::class)->makePartial();
        $file->setIdentifier('/some/identifier.pdf');

        $this->mockedSmServices['FileUploader']->shouldReceive('setFile')
            ->once()
            ->with(m::type(File::class))
            ->andReturnUsing(
                function (File $file) {
                    $this->assertEquals('foo.pdf', $file->getName());
                    $this->assertEquals('<foo>', $file->getContent());
                    $this->assertEquals(5, $file->getSize());
                }
            )
            ->shouldReceive('upload')
            ->with('/some/identifier.pdf')
            ->andReturn($file);

        $result = new Result();
        $result->addMessage('CreateDocument');
        $data = [
            'identifier' => '/some/identifier.pdf',
            'filename' => '/some/identifier.pdf',
            'description' => 'foo',
            'user' => 1
        ];
        $this->expectedSideEffect(CreateDocument::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'identifier' => '/some/identifier.pdf'
            ],
            'messages' => [
                'File uploaded',
                'CreateDocument'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function provideLinkedEntity()
    {
        return [
            [
                'licence',
                111,
                Entity\Licence\Licence::class
            ],
            [
                'application',
                222,
                Entity\Application\Application::class
            ],
            [
                'case',
                333,
                Entity\Cases\Cases::class
            ],
            [
                'transportManager',
                444,
                Entity\Tm\TransportManager::class
            ],
            [
                'busReg',
                555,
                Entity\Bus\BusReg::class
            ],
            [
                'irfoOrganisation',
                666,
                Entity\Organisation\Organisation::class
            ]
        ];
    }
}
