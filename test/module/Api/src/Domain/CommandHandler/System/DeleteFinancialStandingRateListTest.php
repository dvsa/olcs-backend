<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\System;

use Dvsa\Olcs\Api\Domain\CommandHandler\System\DeleteFinancialStandingRateList as CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\FinancialStandingRate as Repo;
use Dvsa\Olcs\Api\Entity\System\FinancialStandingRate as Entity;
use Dvsa\Olcs\Transfer\Command\System\DeleteFinancialStandingRateList as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * DeleteFinancialStandingRateList Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class DeleteFinancialStandingRateListTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('FinancialStandingRate', Repo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'ids' => [69, 99]
        ];
        $command = Command::create($data);

        $mockRate1 = m::mock(Entity::class);
        $mockRate2 = m::mock(Entity::class);

        $this->repoMap['FinancialStandingRate']
            ->shouldReceive('fetchById')
            ->with(69)
            ->once()
            ->andReturn($mockRate1)
            ->shouldReceive('fetchById')
            ->with(99)
            ->once()
            ->andReturn($mockRate2)
            ->shouldReceive('delete')
            ->with($mockRate1)
            ->once()
            ->shouldReceive('delete')
            ->with($mockRate2)
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            [
                'Financial Standing Rate ID 69 deleted',
                'Financial Standing Rate ID 99 deleted',
            ],
            $result->getMessages()
        );
    }
}
