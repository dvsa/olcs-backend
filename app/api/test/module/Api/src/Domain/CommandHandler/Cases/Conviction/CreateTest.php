<?php

/**
 * Create Conviction Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Conviction;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Conviction\Create as CreateCommandHandler;
use Dvsa\Olcs\Transfer\Command\Cases\Conviction\Create as CreateCommand;
use Dvsa\Olcs\Api\Domain\Repository\Conviction;
use Dvsa\Olcs\Api\Entity\Cases\Conviction as ConvictionEntity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Entity;

/**
 * Create Conviction Test
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
        $this->mockRepo('Conviction', Conviction::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'conv_c_cat_1',
            'def_t_dir',
        ];

        $this->references = [
            Entity\Cases\Cases::class => [
                50 => m::mock(Entity\Cases\Cases::class)
            ],
            Entity\Tm\TransportManager::class => [
                55 => m::mock(Entity\Tm\TransportManager::class)
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $id = 111;

        $data = [
            "case" => "50",
            "defendantType" => "def_t_dir",
            "convictionCategory" => "conv_c_cat_1",
            "offenceDate" => "2014-01-01",
            "convictionDate" => "2014-01-02",
            "msi" => "Yes",
            "isDeclared" => "Yes",
            "isDealtWith" => "No",
            "personFirstName" => "Craig",
            "personLastName" => "PA",
            "convictionDate" => "1980-01-02"
        ];

        $command = CreateCommand::create($data);

        /** @var $conv ConvictionEntity */
        $conv = null;

        $this->repoMap['Conviction']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(ConvictionEntity::class))
            ->andReturnUsing(
                function (ConvictionEntity $entity) use (&$conv) {
                    $entity->setId(111);
                    $conv = $entity;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expectedResult = [
            'id' => [
                'conviction' => 111,
            ],
            'messages' => [
                'Conviction Created'
            ]
        ];

        $this->assertEquals($expectedResult, $result->toArray());

        $this->assertEquals(111, $conv->getId());
        $this->assertEquals(50, $conv->getCase()->getId());
    }
}
