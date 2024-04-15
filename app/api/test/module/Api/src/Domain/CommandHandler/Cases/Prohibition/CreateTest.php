<?php

/**
 * Create Prohibition Test
 */

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Prohibition;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Prohibition\Create as CreateCommandHandler;
use Dvsa\Olcs\Transfer\Command\Cases\Prohibition\Create as CreateCommand;
use Dvsa\Olcs\Api\Domain\Repository\Prohibition;
use Dvsa\Olcs\Api\Entity\Prohibition\Prohibition as ProhibitionEntity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractCommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity;

/**
 * Create Prohibition Test
 */
class CreateTest extends AbstractCommandHandlerTestCase
{
    /**
     * @var CreateCommandHandler
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new CreateCommandHandler();
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
        $id = 111;

        $data = [
            "case" => "50",
            "prohibitionType" => "pro_t_si",
            "prohibitionDate" => "2015-01-01",
            "isTrailer" => "N",
            "clearedDate" => "2015-01-02",
        ];

        $command = CreateCommand::create($data);

        /** @var $conv ProhibitionEntity */
        $pro = null;

        $this->repoMap['Prohibition']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(ProhibitionEntity::class))
            ->andReturnUsing(
                function (ProhibitionEntity $entity) use (&$pro) {
                    $entity->setId(111);
                    $pro = $entity;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expectedResult = [
            'id' => [
                'prohibition' => 111,
            ],
            'messages' => [
                'Prohibition Created'
            ]
        ];

        $this->assertEquals($expectedResult, $result->toArray());

        $this->assertEquals(111, $pro->getId());
        $this->assertEquals(50, $pro->getCase()->getId());
    }
}
