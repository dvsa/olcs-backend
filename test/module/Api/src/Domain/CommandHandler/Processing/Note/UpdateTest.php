<?php

/**
 * Update Note Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Note;

use Dvsa\Olcs\Api\Domain\CommandHandler\Processing\Note\Update as UpdateCommandHandler;
use Dvsa\Olcs\Transfer\Command\Processing\Note\Update as UpdateCommand;
use Dvsa\Olcs\Api\Domain\Repository\Note;
use Dvsa\Olcs\Api\Entity\Note\Note as NoteEntity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Entity;

/**
 * Update Note Test
 */
class UpdateTest extends CommandHandlerTestCase
{
    /**
     * @var UpdateCommandHandler
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new UpdateCommandHandler();
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
                55 => m::mock(Entity\Application\Application::class)
            ],
            Entity\System\RefData::class => [
                NoteEntity::NOTE_TYPE_TRANSPORT_MANAGER => m::mock(Entity\System\RefData::class)
            ],
            Entity\User\User::class => [
                1 => m::mock(Entity\User\User::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $id = 150;
        $version = 2;

        $data = [
            'id' => $id,
            'version' => $version,
            'comment' => 'my comment update',
            'priority' => '1'
        ];

        $command = UpdateCommand::create($data);

        $this->repoMap['Note']
            ->shouldReceive('fetchById')
            ->with($id, \Doctrine\Orm\Query::HYDRATE_OBJECT, $version)
            ->andReturn(
                m::mock(NoteEntity::class)
                    ->shouldReceive('setPriority')
                    ->with(1)
                    ->shouldreceive('getId')
                    ->andReturn($id)
                    ->getMock()
            )
            ->shouldReceive('save')
            ->once();

        $result = $this->sut->handleCommand($command);

        $expectedResult = [
            'id' => [
                'note' => $id,
            ],
            'messages' => [
                'Note updated'
            ]
        ];

        $this->assertEquals($expectedResult, $result->toArray());
    }
}
