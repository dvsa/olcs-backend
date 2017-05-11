<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Discs;

use Dvsa\Olcs\Api\Domain\CommandHandler\DataRetention\Populate;
use Dvsa\Olcs\Api\Domain\Repository\DataRetentionRule;
use Dvsa\Olcs\Api\Entity\User\User;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\DataRetention\Populate as Cmd;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class PopulateTest
 */
class PopulateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Populate();
        $this->mockRepo('DataRetentionRule', DataRetentionRule::class);
        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $dataRetentionRules = [
            (new \Dvsa\Olcs\Api\Entity\DataRetentionRule())->setId(2)->setPopulateProcedure('proc2'),
            (new \Dvsa\Olcs\Api\Entity\DataRetentionRule())->setId(9)->setPopulateProcedure('proc9'),
        ];

        $command = Cmd::create([]);
        $this->repoMap['DataRetentionRule']
            ->shouldReceive('fetchEnabledRules')->with()->once()->andReturn($dataRetentionRules)
            ->shouldReceive('runProc')->with('proc2', 222)->once()
            ->shouldReceive('runProc')->with('proc9', 222)->once();

        /** @var User $currentUser */
        $currentUser = m::mock(User::class)->makePartial();
        $currentUser->setId(222);
        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Running rule id 2, proc2',
                'Running rule id 9, proc9',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
