<?php

/**
 * Print Letter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Email\CreateCorrespondenceRecord;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTranslateToWelshTask;
use Dvsa\Olcs\Api\Domain\Exception\RequiresConfirmationException;
use Mockery as m;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\CommandHandler\Document\PrintLetter;
use Dvsa\Olcs\Transfer\Command\Document\PrintLetter as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Print Letter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PrintLetterTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PrintLetter();
        $this->mockRepo('Document', Repository\Document::class);
        $this->mockRepo('DocTemplate', Repository\DocTemplate::class);

        parent::setUp();
    }

    /**
     * @dataProvider documentLicenceRelationship
     *
     * @param Entity\Doc\Document $document
     * @param mixed $parentEntity The entity which contains the licence
     */
    public function testHandleCommandShouldEmailRequireConfirmation(Entity\Doc\Document $document, $parentEntity)
    {
        $data = [
            'id' => 111,
            'shouldEmail' => null
        ];

        $command = Cmd::create($data);

        $document->setMetadata('{"details":{"documentTemplate":222}}');

        $this->repoMap['Document']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($document);

        /** @var Entity\Organisation\Organisation $organisation */
        $organisation = m::mock(Entity\Organisation\Organisation::class)->makePartial();
        $organisation->setAllowEmail(true);

        /** @var Entity\Licence\Licence $licence */
        $licence = m::mock(Entity\Licence\Licence::class)->makePartial();
        $licence->setOrganisation($organisation);

        $parentEntity->setLicence($licence);

        /** @var Entity\Doc\DocTemplate $template */
        $template = m::mock(Entity\Doc\DocTemplate::class)->makePartial();
        $template->setSuppressFromOp(false);

        $this->repoMap['DocTemplate']->shouldReceive('fetchById')
            ->with(222)
            ->andReturn($template);

        $this->setExpectedException(RequiresConfirmationException::class);

        $this->sut->handleCommand($command);
    }

    /**
     * @dataProvider documentLicenceRelationship
     *
     * @param Entity\Doc\Document $document
     * @param mixed $parentEntity The entity which contains the licence
     */
    public function testHandleCommandShouldEmailYes(Entity\Doc\Document $document, $parentEntity)
    {
        $data = [
            'id' => 111,
            'shouldEmail' => 'Y'
        ];

        $command = Cmd::create($data);

        $document->setId(111);
        $document->setMetadata('{"details":{"documentTemplate":222}}');

        $this->repoMap['Document']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($document);

        /** @var Entity\Organisation\Organisation $organisation */
        $organisation = m::mock(Entity\Organisation\Organisation::class)->makePartial();
        $organisation->setAllowEmail(true);

        /** @var Entity\Licence\Licence $licence */
        $licence = m::mock(Entity\Licence\Licence::class)->makePartial();
        $licence->setOrganisation($organisation);
        $licence->setId(333);

        $parentEntity->setLicence($licence);

        /** @var Entity\Doc\DocTemplate $template */
        $template = m::mock(Entity\Doc\DocTemplate::class)->makePartial();
        $template->setSuppressFromOp(false);

        $this->repoMap['DocTemplate']->shouldReceive('fetchById')
            ->with(222)
            ->andReturn($template);

        $data = [
            'licence' => 333,
            'document' => 111,
            'type' => CreateCorrespondenceRecord::TYPE_STANDARD
        ];
        $result = new Result();
        $result->addMessage('CreateCorrespondenceRecord');
        $this->expectedSideEffect(CreateCorrespondenceRecord::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'CreateCorrespondenceRecord'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @dataProvider documentLicenceRelationship
     *
     * @param Entity\Doc\Document $document
     * @param mixed $parentEntity The entity which contains the licence
     */
    public function testHandleCommandShouldEmailNoTranslateToWelsh(Entity\Doc\Document $document, $parentEntity)
    {
        $data = [
            'id' => 111,
            'shouldEmail' => 'N'
        ];

        $command = Cmd::create($data);

        $document->setId(111);
        $document->setMetadata('{"details":{"documentTemplate":222}}');
        $document->setDescription('foo');

        $this->repoMap['Document']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($document);

        /** @var Entity\Organisation\Organisation $organisation */
        $organisation = m::mock(Entity\Organisation\Organisation::class)->makePartial();
        $organisation->setAllowEmail(true);

        /** @var Entity\Licence\Licence $licence */
        $licence = m::mock(Entity\Licence\Licence::class)->makePartial();
        $licence->setOrganisation($organisation);
        $licence->setId(333);
        $licence->setTranslateToWelsh(1);

        $parentEntity->setLicence($licence);

        /** @var Entity\Doc\DocTemplate $template */
        $template = m::mock(Entity\Doc\DocTemplate::class)->makePartial();
        $template->setSuppressFromOp(false);

        $this->repoMap['DocTemplate']->shouldReceive('fetchById')
            ->with(222)
            ->andReturn($template);

        $data = [
            'description' => 'foo',
            'licence' => 333
        ];
        $result = new Result();
        $result->addMessage('CreateTranslateToWelshTask');
        $this->expectedSideEffect(CreateTranslateToWelshTask::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'CreateTranslateToWelshTask'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @dataProvider documentLicenceRelationship
     *
     * @param Entity\Doc\Document $document
     * @param mixed $parentEntity The entity which contains the licence
     */
    public function testHandleCommandShouldEmailNo(Entity\Doc\Document $document, $parentEntity)
    {
        $data = [
            'id' => 111,
            'shouldEmail' => 'N'
        ];

        $command = Cmd::create($data);

        $document->setId(111);
        $document->setMetadata('{"details":{"documentTemplate":222}}');
        $document->setDescription('foo');
        $document->setIdentifier(12345);

        $this->repoMap['Document']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($document);

        /** @var Entity\Organisation\Organisation $organisation */
        $organisation = m::mock(Entity\Organisation\Organisation::class)->makePartial();
        $organisation->setAllowEmail(true);

        /** @var Entity\Licence\Licence $licence */
        $licence = m::mock(Entity\Licence\Licence::class)->makePartial();
        $licence->setOrganisation($organisation);
        $licence->setId(333);
        $licence->setTranslateToWelsh(0);

        $parentEntity->setLicence($licence);

        /** @var Entity\Doc\DocTemplate $template */
        $template = m::mock(Entity\Doc\DocTemplate::class)->makePartial();
        $template->setSuppressFromOp(false);

        $this->repoMap['DocTemplate']->shouldReceive('fetchById')
            ->with(222)
            ->andReturn($template);

        $data = [
            'jobName' => 'foo',
            'documentId' => 111
        ];
        $result = new Result();
        $result->addMessage('Enqueue');
        $this->expectedSideEffect(Enqueue::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Enqueue'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @dataProvider documentLicenceRelationship
     *
     * @param Entity\Doc\Document $document
     * @param mixed $parentEntity The entity which contains the licence
     */
    public function testHandleCommandShouldntEmailSupressed(Entity\Doc\Document $document, $parentEntity)
    {
        $data = [
            'id' => 111
        ];

        $command = Cmd::create($data);

        $document->setId(111);
        $document->setMetadata('{"details":{"documentTemplate":222}}');
        $document->setDescription('foo');
        $document->setIdentifier(12345);

        $this->repoMap['Document']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($document);

        /** @var Entity\Organisation\Organisation $organisation */
        $organisation = m::mock(Entity\Organisation\Organisation::class)->makePartial();
        $organisation->setAllowEmail(true);

        /** @var Entity\Licence\Licence $licence */
        $licence = m::mock(Entity\Licence\Licence::class)->makePartial();
        $licence->setOrganisation($organisation);
        $licence->setId(333);
        $licence->setTranslateToWelsh(0);

        $parentEntity->setLicence($licence);

        /** @var Entity\Doc\DocTemplate $template */
        $template = m::mock(Entity\Doc\DocTemplate::class)->makePartial();
        $template->setSuppressFromOp(true);

        $this->repoMap['DocTemplate']->shouldReceive('fetchById')
            ->with(222)
            ->andReturn($template);

        $data = [
            'jobName' => 'foo',
            'documentId' => 111
        ];
        $result = new Result();
        $result->addMessage('Enqueue');
        $this->expectedSideEffect(Enqueue::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Enqueue'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @dataProvider documentLicenceRelationship
     *
     * @param Entity\Doc\Document $document
     * @param mixed $parentEntity The entity which contains the licence
     */
    public function testHandleCommandShouldntEmailNoLicence(Entity\Doc\Document $document, $parentEntity)
    {
        $data = [
            'id' => 111
        ];

        $command = Cmd::create($data);

        $document->setId(111);
        $document->setMetadata('{"details":{"documentTemplate":222}}');
        $document->setDescription('foo');
        $document->setIdentifier(12345);

        $this->repoMap['Document']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($document);

        /** @var Entity\Doc\DocTemplate $template */
        $template = m::mock(Entity\Doc\DocTemplate::class)->makePartial();
        $template->setSuppressFromOp(false);

        $this->repoMap['DocTemplate']->shouldReceive('fetchById')
            ->with(222)
            ->andReturn($template);

        $data = [
            'jobName' => 'foo',
            'documentId' => 111
        ];
        $result = new Result();
        $result->addMessage('Enqueue');
        $this->expectedSideEffect(Enqueue::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Enqueue'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function documentLicenceRelationship()
    {
        /** @var Entity\Doc\Document $docWithLicence */
        $docWithLicence = m::mock(Entity\Doc\Document::class)->makePartial();

        /** @var Entity\Application\Application $application */
        $application = m::mock(Entity\Application\Application::class)->makePartial();
        /** @var Entity\Doc\Document $docWithApplication */
        $docWithApplication = m::mock(Entity\Doc\Document::class)->makePartial();
        $docWithApplication->setApplication($application);

        /** @var Entity\Cases\Cases $case */
        $case = m::mock(Entity\Cases\Cases::class)->makePartial();
        /** @var Entity\Doc\Document $docWithCase */
        $docWithCase = m::mock(Entity\Doc\Document::class)->makePartial();
        $docWithCase->setCase($case);

        /** @var Entity\Bus\BusReg $busReg */
        $busReg = m::mock(Entity\Bus\BusReg::class)->makePartial();
        /** @var Entity\Doc\Document $docWithBusReg */
        $docWithBusReg = m::mock(Entity\Doc\Document::class)->makePartial();
        $docWithBusReg->setBusReg($busReg);

        return [
            [
                $docWithLicence,
                $docWithLicence
            ],
            [
                $docWithApplication,
                $application
            ],
            [
                $docWithCase,
                $case
            ],
            [
                $docWithBusReg,
                $busReg
            ]
        ];
    }
}
