<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TaskAllocationRule;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\TaskAllocationRule\Create as CommandHandler;
use Dvsa\Olcs\Transfer\Command\TaskAllocationRule\Create as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\User\Team as TeamEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;

/**
 * TaskAllocationRule CreateTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CreateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('TaskAllocationRule', \Dvsa\Olcs\Api\Domain\Repository\TaskAllocationRule::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            CategoryEntity::class => [
                1 => m::mock(CategoryEntity::class)
            ],
            TeamEntity::class => [
                2 => m::mock(TeamEntity::class)
            ],
            UserEntity::class => [
                3 => m::mock(UserEntity::class)
            ],
            TrafficAreaEntity::class => [
                'T' => m::mock(UserEntity::class)
            ],
        ];

        $this->refData = ['lcat_gv', 'lcat_psv'];

        parent::initReferences();
    }

    public function testHandleCommandAllParams()
    {
        $command = Cmd::create(
            [
                'category' => 1,
                'team' => 2,
                'user' => 3,
                'goodsOrPsv' => 'lcat_gv',
                'isMlh' => 'Y',
                'trafficArea' => 'T',
            ]
        );

        $this->repoMap['TaskAllocationRule']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Task\TaskAllocationRule $tar) {
                $this->assertSame($this->references[CategoryEntity::class][1], $tar->getCategory());
                $this->assertSame($this->references[TeamEntity::class][2], $tar->getTeam());
                $this->assertSame($this->references[UserEntity::class][3], $tar->getUser());
                $this->assertSame($this->refData['lcat_gv'], $tar->getGoodsOrPsv());
                $this->assertSame(true, $tar->getIsMlh());
                $this->assertSame($this->references[TrafficAreaEntity::class]['T'], $tar->getTrafficArea());
                $tar->setId(1304);
            }
        );

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            [
                'id' => [
                    'task-allocation-rule' => 1304,
                ],
                'messages' => [
                    'TaskAllocationRule created',
                ]
            ],
            $result->toArray()
        );
    }

    public function testHandleCommandMinParams()
    {
        $command = Cmd::create(
            [
                'category' => 1,
                'team' => 2,
            ]
        );

        $this->repoMap['TaskAllocationRule']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Task\TaskAllocationRule $tar) {
                $this->assertSame($this->references[CategoryEntity::class][1], $tar->getCategory());
                $this->assertSame($this->references[TeamEntity::class][2], $tar->getTeam());
                $this->assertSame(null, $tar->getUser());
                $this->assertSame(null, $tar->getGoodsOrPsv());
                $this->assertSame(false, $tar->getIsMlh());
                $this->assertSame(null, $tar->getTrafficArea());
                $tar->setId(1304);
            }
        );

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            [
                'id' => [
                    'task-allocation-rule' => 1304,
                ],
                'messages' => [
                    'TaskAllocationRule created',
                ]
            ],
            $result->toArray()
        );
    }
}
