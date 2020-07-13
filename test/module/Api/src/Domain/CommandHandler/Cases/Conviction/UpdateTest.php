<?php

/**
 * Update Conviction Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Conviction;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Conviction\Update as UpdateCommandHandler;
use Dvsa\Olcs\Transfer\Command\Cases\Conviction\Update as UpdateCommand;
use Dvsa\Olcs\Api\Domain\Repository\Conviction;
use Dvsa\Olcs\Api\Entity\Cases\Conviction as ConvictionEntity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Entity;

/**
 * Update Conviction Test
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
        $id = 150;
        $case = 50;
        $version = 2;

        $data = [
            "id" => $id,
            "version" => $version,
            "case" => $case,
            "defendantType" => "def_t_dir",
            "convictionCategory" => "conv_c_cat_1",
            "offenceDate" => "2014-01-01",
            "convictionDate" => "2014-01-02",
            "msi" => "Y",
            "isDeclared" => "Y",
            "isDealtWith" => "N",
            "personFirstName" => "Craig",
            "personLastName" => "PA",
            "convictionDate" => "1980-01-02"
        ];

        $command = UpdateCommand::create($data);

        /** @var $note ConvictionEntity */
        $note = null;

        $this->repoMap['Conviction']
            ->shouldReceive('fetchById')
            ->with($id, \Doctrine\Orm\Query::HYDRATE_OBJECT, $version)
            ->andReturn(
                m::mock(ConvictionEntity::class)
                    ->shouldReceive('setPersonFirstName')
                    ->with('Craig')

                    ->shouldReceive('updateConvictionCategory')
                    ->with(m::type(Entity\System\RefData::class), null)

                    ->shouldReceive('setDefendantType')
                    ->andReturn(
                        m::mock(Entity\System\RefData::class)
                            ->shouldReceive('getId')
                            ->andReturn('def_t_dir')
                            ->getMock()
                    )

                    // Get ID
                    ->shouldreceive('getId')
                    ->andReturn($id)

                    // Person
                    ->shouldreceive('setPersonFirstname')
                    ->with("Craig")

                    ->shouldreceive('setPersonLastname')
                    ->with("PA")

                    ->shouldreceive('setBirthDate')
                    ->with(m::type(\DateTime::class))

                    ->shouldreceive('setOffenceDate')
                    ->with(m::type(\DateTime::class))

                    ->shouldreceive('setConvictionDate')
                    ->with(m::type(\DateTime::class))

                    ->shouldreceive('setMsi')
                    ->with("Y")

                    ->shouldreceive('setIsDeclared')
                    ->with("Y")

                    ->shouldreceive('setIsDealtWith')
                    ->with("N")

                    ->getMock()
            )
            ->shouldReceive('save')
            ->once();

        $result = $this->sut->handleCommand($command);

        $expectedResult = [
            'id' => [
                'conviction' => $id,
            ],
            'messages' => [
                'Conviction Updated'
            ]
        ];

        $this->assertEquals($expectedResult, $result->toArray());
    }
}
