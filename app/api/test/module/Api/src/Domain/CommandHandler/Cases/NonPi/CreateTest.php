<?php

/**
 * Create NonPi Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\NonPi;

use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\NonPi\Create as CreateCommandHandler;
use Dvsa\Olcs\Transfer\Command\Cases\NonPi\Create as CreateCommand;
use Dvsa\Olcs\Api\Domain\Repository\NonPi;
use Dvsa\Olcs\Api\Entity\Cases\Hearing as NonPiEntity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Entity;

/**
 * Create NonPi Test
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
        $this->mockRepo('NonPi', NonPi::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'non_pi_type_off_proc',
            'non_pio_nfa'
        ];

        $this->references = [
            Entity\Cases\Cases::class => [
                50 => m::mock(Entity\Cases\Cases::class)
            ],
            Entity\Venue::class => [
                2 => m::mock(Entity\Venue::class)
            ]
        ];

        parent::initReferences();
    }

    /**
     * @dataProvider dpWitnessProvider
     */
    public function testHandleCommand($inputWitnesses, $outputWitnesses)
    {
        $data = [
            "case" => "50",
            "nonPiType" => "non_pi_type_off_proc",
            "venue" => "2",
            "presidingStaffName" => "John Smith",
            "agreedByTcDate" => "2015-01-01 12:15",
            "hearingDate" => "2015-02-01 14:15",
            "venueOther" => "Some Other Venue",
            "witnessCount" => $inputWitnesses,
            "outcome" => "non_pio_nfa"
        ];

        $command = CreateCommand::create($data);

        /** @var $conv NonPiEntity */
        $pro = null;

        $this->repoMap['NonPi']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(NonPiEntity::class))
            ->andReturnUsing(
                function (NonPiEntity $entity) use (&$pro) {
                    $entity->setId(111);
                    $pro = $entity;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expectedResult = [
            'id' => [
                'non-pi' => 111,
            ],
            'messages' => [
                'Created'
            ]
        ];

        $this->assertEquals($expectedResult, $result->toArray());

        /** @var NonPiEntity $pro */
        $this->assertEquals(111, $pro->getId());
        $this->assertEquals(50, $pro->getCase()->getId());
        $this->assertEquals($outputWitnesses, $pro->getWitnessCount());
    }

    /**
     * expected witness input and output values
     */
    public function dpWitnessProvider()
    {
        return [
            [4, 4],
            [1, 1],
            [0, 0],
            [null, 0]
        ];
    }
}
