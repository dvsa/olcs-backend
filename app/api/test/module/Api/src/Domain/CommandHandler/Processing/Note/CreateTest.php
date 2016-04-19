<?php

/**
 * Create Note Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Note;

use Dvsa\Olcs\Api\Domain\CommandHandler\Processing\Note\Create as CreateCommandHandler;
use Dvsa\Olcs\Transfer\Command\Processing\Note\Create as CreateCommand;
use Dvsa\Olcs\Api\Domain\Repository\Note;
use Dvsa\Olcs\Api\Entity\Note\Note as NoteEntity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity;

/**
 * Create Note Test
 */
class CreateTest extends CommandHandlerTestCase
{
    /**
     * @var CreateCommandHandler
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CreateCommandHandler();
        $this->mockRepo('Note', Note::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            NoteEntity::NOTE_TYPE_APPLICATION,
            NoteEntity::NOTE_TYPE_BUS,
            NoteEntity::NOTE_TYPE_CASE,
            NoteEntity::NOTE_TYPE_LICENCE,
            NoteEntity::NOTE_TYPE_ORGANISATION,
            NoteEntity::NOTE_TYPE_PERSON,
            NoteEntity::NOTE_TYPE_TRANSPORT_MANAGER
        ];

        $this->references = [
            Entity\Application\Application::class => [
                50 => m::mock(Entity\Application\Application::class)
            ],
            Entity\Bus\BusReg::class => [
                51 => m::mock(Entity\Bus\BusReg::class)
            ],
            Entity\Cases\Cases::class => [
                52 => m::mock(Entity\Cases\Cases::class)
            ],
            Entity\Licence\Licence::class => [
                53 => m::mock(Entity\Licence\Licence::class)
            ],
            Entity\Organisation\Organisation::class => [
                54 => m::mock(Entity\Organisation\Organisation::class)
            ],
            Entity\Tm\TransportManager::class => [
                55 => m::mock(Entity\Tm\TransportManager::class)
            ],
            Entity\System\RefData::class => [
                NoteEntity::NOTE_TYPE_TRANSPORT_MANAGER => m::mock(Entity\System\RefData::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'application' => 50,
            'busReg' => 51,
            'case' => 52,
            'licence' => 53,
            'organisation' => 54,
            'transportManager' => 55,
            'comment' => 'my comment'
        ];

        $command = CreateCommand::create($data);

        /** @var $note NoteEntity */
        $note = null;

        $this->repoMap['Note']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(NoteEntity::class))
            ->andReturnUsing(
                function (NoteEntity $noteEntity) use (&$note) {
                    $noteEntity->setId(111);
                    $note = $noteEntity;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expectedResult = [
            'id' => [
                'note' => 111,
            ],
            'messages' => [
                'Note created'
            ]
        ];

        $this->assertEquals($expectedResult, $result->toArray());

        $this->assertEquals(111, $note->getId());

        $this->assertEquals(50, $note->getApplication()->getId());
        $this->assertEquals(51, $note->getBusReg()->getId());
        $this->assertEquals(52, $note->getCase()->getId());
        $this->assertEquals(53, $note->getLicence()->getId());
        $this->assertEquals(54, $note->getOrganisation()->getId());
        $this->assertEquals(55, $note->getTransportManager()->getId());

        // Because we set the TM last, it will be a TM note Type.
        $this->assertEquals(NoteEntity::NOTE_TYPE_TRANSPORT_MANAGER, $note->getNoteType()->getId());
    }
}
