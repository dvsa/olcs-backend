<?php

/**
 * Create NonPi Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\NonPi;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
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
        ];

        $this->references = [
            Entity\Cases\Cases::class => [
                50 => m::mock(Entity\Cases\Cases::class)
            ],
            Entity\Pi\PiVenue::class => [
                2 => m::mock(Entity\Pi\PiVenue::class)
            ],
            Entity\Pi\PresidingTc::class => [
                2 => m::mock(Entity\Pi\PresidingTc::class)
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $id = 111;

        $data = [
            "case" => "50",
            "nonPiType" => "non_pi_type_off_proc",
            "venue" => "2",
            "presidingTc" => "2",
            "agreedByTcDate" => "2015-01-01 12:15",
            "hearingDate" => "2015-02-01 14:15",
            "venueOther" => "Some Other Venue",
            "witnessCount" => "4"
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

        $this->assertEquals(111, $pro->getId());
        $this->assertEquals(50, $pro->getCase()->getId());
    }
}
