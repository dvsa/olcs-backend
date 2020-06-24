<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository\DocTemplate;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Document\CreateLetter;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Document\CreateLetter as Cmd;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * Create Letter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateLetterTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CreateLetter();
        $this->mockRepo('DocTemplate', DocTemplate::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $queryData = ['details' => ['category' => '123', 'documentSubCategory' => '321']];
        $expectedQueryData = ['details' => ['category' => '123', 'documentSubCategory' => '321']];

        $data = [
            'template' => 111,
            'data' => $queryData,
            'meta' => 'foo',
            'disableBookmarks' => true
        ];

        $command = Cmd::create($data);

        /** @var Entity\Doc\DocTemplate $template */
        $template = m::mock(Entity\Doc\DocTemplate::class)->makePartial();
        $template->setDescription('Foo-:Bar_Cake Cheese');
        $template->shouldReceive('getDocument->getIdentifier')
            ->andReturn('Foo-Bar_Cake Cheese.rtf');

        $this->repoMap['DocTemplate']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($template);

        $result = new Result();
        $result->addMessage('GenerateAndStore');
        $data = [
            'template' => 'Foo-Bar_Cake Cheese.rtf',
            'query' => $expectedQueryData,
            'description' => 'Foo-:Bar_Cake Cheese',
            'category' => '123',
            'subCategory' => '321',
            'isExternal' => false,
            'isScan' => false,
            'metadata' => 'foo',
            'disableBookmarks' => true
        ];
        $this->expectedSideEffect(GenerateAndStore::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'GenerateAndStore'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithException()
    {
        //$this->expectException(\Exception::class, 'Error generating document');
        $this->expectException(ValidationException::class, 'Error generating document');
        $queryData = ['details' => ['category' => '123', 'documentSubCategory' => '321']];
        $expectedQueryData = ['details' => ['category' => '123', 'documentSubCategory' => '321']];

        $data = [
            'template' => 111,
            'data' => $queryData,
            'meta' => 'foo',
            'disableBookmarks' => false
        ];

        $command = Cmd::create($data);

        /** @var Entity\Doc\DocTemplate $template */
        $template = m::mock(Entity\Doc\DocTemplate::class)->makePartial();
        $template->setDescription('Foo-:Bar_Cake Cheese');
        $template->shouldReceive('getDocument->getIdentifier')
            ->andReturn('Foo-Bar_Cake Cheese.rtf');

        $this->repoMap['DocTemplate']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($template);

        $result = new Result();
        $result->addMessage('GenerateAndStore');
        $data = [
            'template' => 'Foo-Bar_Cake Cheese.rtf',
            'query' => $expectedQueryData,
            'description' => 'Foo-:Bar_Cake Cheese',
            'category' => '123',
            'subCategory' => '321',
            'isExternal' => false,
            'isScan' => false,
            'metadata' => 'foo',
            'disableBookmarks' => false
        ];
        $this->expectedSideEffectThrowsException(
            GenerateAndStore::class,
            $data,
            new \Exception('Error generating document')
        );
        $this->sut->handleCommand($command);
    }
}
