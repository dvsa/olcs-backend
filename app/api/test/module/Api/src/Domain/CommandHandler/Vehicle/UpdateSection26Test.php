<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Vehicle;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Vehicle\UpdateSection26 as CommandHandler;
use Dvsa\Olcs\Transfer\Command\Vehicle\UpdateSection26 as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;

/**
 * UpdateSection26Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UpdateSection26Test extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Vehicle', \Dvsa\Olcs\Api\Domain\Repository\Vehicle::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommandY()
    {
        $data = [
            'ids' => [
                111, 222, 333,
            ],
            'section26' => 'Y'
        ];
        $command = Cmd::create($data);

        $vehicle111 = new Vehicle();
        $vehicle111->setSection26(false);
        $vehicle222 = new Vehicle();
        $vehicle222->setSection26(false);
        $vehicle333 = new Vehicle();
        $vehicle333->setSection26(false);

        $this->repoMap['Vehicle']->shouldReceive('fetchById')->with(111)->once()->andReturn($vehicle111);
        $this->repoMap['Vehicle']->shouldReceive('fetchById')->with(222)->once()->andReturn($vehicle222);
        $this->repoMap['Vehicle']->shouldReceive('fetchById')->with(333)->once()->andReturn($vehicle333);

        $this->repoMap['Vehicle']->shouldReceive('save')->with($vehicle111)->once();
        $this->repoMap['Vehicle']->shouldReceive('save')->with($vehicle222)->once();
        $this->repoMap['Vehicle']->shouldReceive('save')->with($vehicle333)->once();

        $result = $this->sut->handleCommand($command);

        $this->assertTrue($vehicle111->getSection26());
        $this->assertTrue($vehicle222->getSection26());
        $this->assertTrue($vehicle333->getSection26());

        $expected = [
            'id' => [],
            'messages' => [
                'Updated Section26 on 3 Vehicle(s).',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandN()
    {
        $data = [
            'ids' => [
                111, 222, 333,
            ],
            'section26' => 'F'
        ];
        $command = Cmd::create($data);

        $vehicle111 = new Vehicle();
        $vehicle111->setSection26(true);
        $vehicle222 = new Vehicle();
        $vehicle222->setSection26(true);
        $vehicle333 = new Vehicle();
        $vehicle333->setSection26(true);

        $this->repoMap['Vehicle']->shouldReceive('fetchById')->with(111)->once()->andReturn($vehicle111);
        $this->repoMap['Vehicle']->shouldReceive('fetchById')->with(222)->once()->andReturn($vehicle222);
        $this->repoMap['Vehicle']->shouldReceive('fetchById')->with(333)->once()->andReturn($vehicle333);

        $this->repoMap['Vehicle']->shouldReceive('save')->with($vehicle111)->once();
        $this->repoMap['Vehicle']->shouldReceive('save')->with($vehicle222)->once();
        $this->repoMap['Vehicle']->shouldReceive('save')->with($vehicle333)->once();

        $result = $this->sut->handleCommand($command);

        $this->assertFalse($vehicle111->getSection26());
        $this->assertFalse($vehicle222->getSection26());
        $this->assertFalse($vehicle333->getSection26());

        $expected = [
            'id' => [],
            'messages' => [
                'Updated Section26 on 3 Vehicle(s).',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
