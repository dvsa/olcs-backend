<?php

/**
 * Update NonPi Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\NonPi;

use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\NonPi\Update as UpdateCommandHandler;
use Dvsa\Olcs\Transfer\Command\Cases\NonPi\Update as UpdateCommand;
use Dvsa\Olcs\Api\Domain\Repository\NonPi;
use Dvsa\Olcs\Api\Entity\Cases\Hearing as NonPiEntity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Entity;

/**
 * Update NonPi Test
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
        $this->mockRepo('NonPi', NonPi::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'non_pi_type_off_proc',
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
        $id = 150;
        $version = 2;

        $agreedByTcDate = "2015-02-01 14:15";
        $agreedByTcDateTime = new \DateTime($agreedByTcDate);
        $hearingDate = "2015-02-01 14:15";
        $hearingDateTime = new \DateTime($hearingDate);

        $data = [
            "id" => $id,
            "version" => $version,
            "case" => "50",
            "nonPiType" => "non_pi_type_off_proc",
            "venue" => "2",
            "presidingStaffName" => "Ed",
            "agreedByTcDate" => $agreedByTcDate,
            "hearingDate" => $hearingDate,
            "witnessCount" => $inputWitnesses,
            "outcome" => "non_pio_nfa"
        ];

        $command = UpdateCommand::create($data);

        /** @var $note NonPiEntity */
        $note = null;

        $this->repoMap['NonPi']
            ->shouldReceive('fetchById')
            ->with($id, \Doctrine\Orm\Query::HYDRATE_OBJECT, $version)
            ->andReturn(
                m::mock(NonPiEntity::class)
                    ->shouldReceive('setHearingType')
                    ->andReturn(
                        m::mock(Entity\System\RefData::class)
                            ->shouldReceive('getId')
                            ->andReturn('non_pi_type_off_proc')
                            ->getMock()
                    )
                    ->shouldreceive('getId')
                    ->andReturn($id)
                    ->shouldreceive('setCase')
                    ->with(m::type(Entity\Cases\Cases::class))
                    ->shouldreceive('setVenue')
                    ->with(m::type(Entity\Venue::class))
                    ->shouldreceive('setVenueOther')
                    ->with(null)
                    ->shouldreceive('setPresidingStaffName')
                    ->with('Ed')
                    ->shouldReceive('processDate')
                    ->with($agreedByTcDate)
                    ->once()
                    ->andReturn($agreedByTcDateTime)
                    ->shouldreceive('setAgreedByTcDate')
                    ->with($agreedByTcDateTime)
                    ->shouldReceive('processDate')
                    ->with($hearingDate, \DateTime::ISO8601, false)
                    ->once()
                    ->andReturn($hearingDateTime)
                    ->shouldreceive('setHearingDate')
                    ->with($hearingDateTime)
                    ->shouldreceive('setWitnessCount')
                    ->with($outputWitnesses)
                    ->shouldReceive('setOutcome')
                    ->andReturn(
                        m::mock(Entity\System\RefData::class)
                            ->shouldReceive('getId')
                            ->andReturn('non_pio_nfa')
                            ->getMock()
                    )
                    ->getMock()
            )
            ->shouldReceive('save')
            ->once();

        $result = $this->sut->handleCommand($command);

        $expectedResult = [
            'id' => [
                'non-pi' => $id,
            ],
            'messages' => [
                'Updated'
            ]
        ];

        $this->assertEquals($expectedResult, $result->toArray());
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
