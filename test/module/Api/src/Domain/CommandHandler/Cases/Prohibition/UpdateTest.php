<?php

/**
 * Update Prohibition Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Prohibition;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Prohibition\Update as UpdateCommandHandler;
use Dvsa\Olcs\Transfer\Command\Cases\Prohibition\Update as UpdateCommand;
use Dvsa\Olcs\Api\Domain\Repository\Prohibition;
use Dvsa\Olcs\Api\Entity\Prohibition\Prohibition as ProhibitionEntity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Entity;

/**
 * Update Prohibition Test
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
        $this->mockRepo('Prohibition', Prohibition::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'pro_t_si',
        ];

        $this->references = [
            Entity\Cases\Cases::class => [
                50 => m::mock(Entity\Cases\Cases::class)
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
            "prohibitionType" => "pro_t_si",
            "prohibitionDate" => "2015-01-01",
            "isTrailer" => "N",
            "clearedDate" => "2015-01-02",
            "ImposedAt" => 'test imposed at',
        ];

        $command = UpdateCommand::create($data);

        $this->repoMap['Prohibition']
            ->shouldReceive('fetchById')
            ->with($id, \Doctrine\Orm\Query::HYDRATE_OBJECT, $version)
            ->andReturn(
                m::mock(ProhibitionEntity::class)

                    ->shouldReceive('setProhibitionType')
                    ->andReturn(
                        m::mock(Entity\System\RefData::class)
                            ->shouldReceive('getId')
                            ->andReturn('pro_t_si')
                            ->getMock()
                    )

                    // Get ID
                    ->shouldReceive('getId')
                    ->andReturn($id)

                    ->shouldReceive('setProhibitionDate')
                    ->with("Yes")

                    ->shouldReceive('setProhibitionDate')
                    ->with(m::type(\DateTime::class))

                    ->shouldReceive('setClearedDate')
                    ->with(m::type(\DateTime::class))

                    ->shouldReceive('setIsTrailer')
                    ->with("N")

                    ->shouldReceive('setImposedAt')
                    ->with("test imposed at")

                    ->getMock()
            )
            ->shouldReceive('save')
            ->once();

        $result = $this->sut->handleCommand($command);

        $expectedResult = [
            'id' => [
                'prohibition' => $id,
            ],
            'messages' => [
                'Prohibition Updated'
            ]
        ];

        $this->assertEquals($expectedResult, $result->toArray());
    }
}
