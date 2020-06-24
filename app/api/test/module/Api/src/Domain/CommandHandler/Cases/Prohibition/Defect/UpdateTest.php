<?php

/**
 * Update ProhibitionDefect Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Prohibition\Defect;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Prohibition\Defect\Update as UpdateCommandHandler;
use Dvsa\Olcs\Transfer\Command\Cases\Prohibition\Defect\Update as UpdateCommand;
use Dvsa\Olcs\Api\Domain\Repository\ProhibitionDefect;
use Dvsa\Olcs\Api\Entity\Prohibition\ProhibitionDefect as ProhibitionDefectEntity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Entity;

/**
 * Update Prohibition\Defect Test
 */
class UpdateTest extends CommandHandlerTestCase
{
    /**
     * @var UpdateCommandHandler
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new UpdateCommandHandler();
        $this->mockRepo('ProhibitionDefect', ProhibitionDefect::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'pro_t_si',
        ];

        $this->references = [
            Entity\Prohibition\Prohibition::class => [
                50 => m::mock(Entity\Prohibition\Prohibition::class)
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $id = 150;
        $version = 2;

        $data = [
            "id" => $id,
            "version" => $version,
            "prohibition" => "50",
            "defectType" => "My Defect Type String",
            "notes" => "My Defect Info String"
        ];

        $command = UpdateCommand::create($data);

        /** @var $note ProhibitionDefectEntity */
        $note = null;

        $this->repoMap['ProhibitionDefect']
            ->shouldReceive('fetchById')
            ->with($id, \Doctrine\Orm\Query::HYDRATE_OBJECT, $version)
            ->andReturn(
                m::mock(ProhibitionDefectEntity::class)

                    ->shouldreceive('setProhibition')
                    ->with(m::type(Entity\Prohibition\Prohibition::class))

                    // Get ID
                    ->shouldreceive('getId')
                    ->andReturn($id)

                    ->shouldReceive('setDefectType')
                    ->with("My Defect Type String")

                    ->shouldReceive('setNotes')
                    ->with("My Defect Info String")

                    ->getMock()
            )
            ->shouldReceive('save')
            ->once();

        $result = $this->sut->handleCommand($command);

        $expectedResult = [
            'id' => [
                'defect' => $id,
            ],
            'messages' => [
                'Prohibition Defect Updated'
            ]
        ];

        $this->assertEquals($expectedResult, $result->toArray());
    }
}
