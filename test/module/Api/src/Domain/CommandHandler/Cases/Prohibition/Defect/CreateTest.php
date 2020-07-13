<?php

/**
 * Create Prohibition Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Prohibition\Defect;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Prohibition\Defect\Create as CreateCommandHandler;
use Dvsa\Olcs\Transfer\Command\Cases\Prohibition\Defect\Create as CreateCommand;
use Dvsa\Olcs\Api\Domain\Repository\ProhibitionDefect;
use Dvsa\Olcs\Api\Entity\Prohibition\ProhibitionDefect as ProhibitionDefectEntity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Entity;

/**
 * Create ProhibitionDefect Test
 */
class CreateTest extends CommandHandlerTestCase
{
    /**
     * @var CreateCommandHandler
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new CreateCommandHandler();
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
        $id = 111;

        $data = [
            "prohibition" => "1",
            "defectType" => "My Defect Type String",
            "notes" => "My Defect Info String"
        ];

        $command = CreateCommand::create($data);

        /** @var $pro ProhibitionDefectEntity */
        $pro = null;

        $this->repoMap['ProhibitionDefect']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(ProhibitionDefectEntity::class))
            ->andReturnUsing(
                function (ProhibitionDefectEntity $entity) use (&$pro) {
                    $entity->setId(111);
                    $pro = $entity;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expectedResult = [
            'id' => [
                'defect' => 111,
            ],
            'messages' => [
                'Prohibition Defect Created'
            ]
        ];

        $this->assertEquals($expectedResult, $result->toArray());

        $this->assertEquals(111, $pro->getId());
    }
}
