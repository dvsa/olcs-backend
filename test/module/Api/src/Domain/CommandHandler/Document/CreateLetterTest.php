<?php

/**
 * Create Letter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository\DocTemplate;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Service\Document\DocumentGenerator;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Document\CreateLetter;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Document\CreateLetter as Cmd;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity;

/**
 * Create Letter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateLetterTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateLetter();
        $this->mockRepo('DocTemplate', DocTemplate::class);

        $this->mockedSmServices['DocumentGenerator'] = m::mock(DocumentGenerator::class);
        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];
        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $queryData = ['details' => ['category' => '123', 'documentSubCategory' => '321']];
        $expectedQueryData = ['details' => ['category' => '123', 'documentSubCategory' => '321'], 'user' => 456];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser->getId')
            ->andReturn(456);

        $data = [
            'template' => 111,
            'data' => $queryData,
            'meta' => 'foo'
        ];

        $content = 'ABCDEF';

        $command = Cmd::create($data);

        $file = m::mock();
        $file->shouldReceive('getIdentifier')
            ->andReturn(12345)
            ->shouldReceive('getSize')
            ->andReturn(100);

        /** @var Entity\Doc\DocTemplate $template */
        $template = m::mock(Entity\Doc\DocTemplate::class)->makePartial();
        $template->setDescription('Foo-:Bar_Cake Cheese');
        $template->shouldReceive('getDocument->getIdentifier')
            ->andReturn('Foo-Bar_Cake Cheese.rtf');

        $dateTime = new DateTime();
        $date = $dateTime->format('YmdHis');
        $expectedFilename = $date . '_Foo-Bar_Cake_Cheese.rtf';

        $this->mockedSmServices['DocumentGenerator']->shouldReceive('generateFromTemplateIdentifier')
            ->with('Foo-Bar_Cake_Cheese.rtf', $expectedQueryData)
            ->andReturn($content)
            ->shouldReceive('uploadGeneratedContent')
            ->once()
            ->with('ABCDEF', null, $expectedFilename)
            ->andReturn($file);

        $this->repoMap['DocTemplate']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($template);

        $result = new Result();
        $result->addMessage('CreateDocumentSpecific');
        $data = [
            'identifier' => 12345,
            'description' => 'Foo-:Bar_Cake Cheese',
            'filename' => $expectedFilename,
            'category' => '123',
            'subCategory' => '321',
            'isExternal' => false,
            'isScan' => false,
            'metadata' => 'foo',
            'size' => 100
        ];
        $this->expectedSideEffect(CreateDocumentSpecific::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'CreateDocumentSpecific',
                'File created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
