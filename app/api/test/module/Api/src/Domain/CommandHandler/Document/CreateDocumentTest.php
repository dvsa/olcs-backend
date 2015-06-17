<?php

/**
 * Create Document Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Repository\Document;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Document\CreateDocument;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Document\CreateDocument as Cmd;
use Dvsa\Olcs\Api\Entity\Doc\Document as Entity;
use ZfcRbac\Service\AuthorizationService;

/**
 * Create Document Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateDocumentTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateDocument();
        $this->mockRepo('Document', Document::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [
            Application::class => [
                123 => m::mock(Application::class)
            ]
        ];

        $this->categoryReferences = [
            1 => m::mock(Category::class),
            2 => m::mock(Category::class)
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'identifier' => 'ABCDEF',
            'filename' => 'foo.pdf',
            'size' => 1024,
            'category' => 1,
            'subCategory' => 2,
            'application' => 123,
            'issuedDate' => '2015-01-01'
        ];

        $command = Cmd::create($data);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(true);

        $this->repoMap['Document']->shouldReceive('save')
            ->with(m::type(Entity::class))
            ->andReturnUsing(
                function (Entity $document) {
                    $document->setId(111);

                    $this->assertEquals('ABCDEF', $document->getIdentifier());
                    $this->assertTrue($document->getIsExternal());
                    $this->assertEquals('foo.pdf', $document->getFilename());
                    $this->assertEquals(1024, $document->getSize());
                    $this->assertSame($this->categoryReferences[1], $document->getCategory());
                    $this->assertSame($this->categoryReferences[2], $document->getSubCategory());
                    $this->assertSame($this->references[Application::class][123], $document->getApplication());
                    $this->assertNull($document->getLicence());
                    $this->assertInstanceOf('\DateTime', $document->getIssuedDate());
                    $this->assertEquals('2015-01-01', $document->getIssuedDate()->format('Y-m-d'));
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'document' => 111
            ],
            'messages' => [
                'Document created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandInternal()
    {
        $data = [
            'identifier' => 'ABCDEF',
            'filename' => 'foo.pdf',
            'size' => 1024,
            'category' => 1,
            'subCategory' => 2,
            'application' => 123
        ];

        $command = Cmd::create($data);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(false);

        $this->repoMap['Document']->shouldReceive('save')
            ->with(m::type(Entity::class))
            ->andReturnUsing(
                function (Entity $document) {
                    $document->setId(111);

                    $this->assertEquals('ABCDEF', $document->getIdentifier());
                    $this->assertFalse($document->getIsExternal());
                    $this->assertEquals('foo.pdf', $document->getFilename());
                    $this->assertEquals(1024, $document->getSize());
                    $this->assertSame($this->categoryReferences[1], $document->getCategory());
                    $this->assertSame($this->categoryReferences[2], $document->getSubCategory());
                    $this->assertSame($this->references[Application::class][123], $document->getApplication());
                    $this->assertNull($document->getLicence());
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'document' => 111
            ],
            'messages' => [
                'Document created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandIsExternalSet()
    {
        $data = [
            'identifier' => 'ABCDEF',
            'filename' => 'foo.pdf',
            'size' => 1024,
            'category' => 1,
            'subCategory' => 2,
            'application' => 123,
            'isExternal' => true
        ];

        $command = Cmd::create($data);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(false);

        $this->repoMap['Document']->shouldReceive('save')
            ->with(m::type(Entity::class))
            ->andReturnUsing(
                function (Entity $document) {
                    $document->setId(111);

                    $this->assertEquals('ABCDEF', $document->getIdentifier());
                    $this->assertTrue($document->getIsExternal());
                    $this->assertEquals('foo.pdf', $document->getFilename());
                    $this->assertEquals(1024, $document->getSize());
                    $this->assertSame($this->categoryReferences[1], $document->getCategory());
                    $this->assertSame($this->categoryReferences[2], $document->getSubCategory());
                    $this->assertSame($this->references[Application::class][123], $document->getApplication());
                    $this->assertNull($document->getLicence());
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'document' => 111
            ],
            'messages' => [
                'Document created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
