<?php

/**
 * DeleteConditionUndertakingS4Test.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\ConditionUndertaking;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Application\S4;
use Dvsa\Olcs\Transfer\Query\LicenceOperatingCentre\LicenceOperatingCentre;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\ConditionUndertaking\DeleteConditionUndertakingS4;
use Dvsa\Olcs\Api\Domain\Repository\ConditionUndertaking as ConditionUndertakingRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Cases\ConditionUndertaking\DeleteConditionUndertakingS4 as Cmd;
use Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking as ConditionUndertakingEntity;

/**
 * Class DeleteConditionUndertakingS4Test
 * @package Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\ConditionUndertaking
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class DeleteConditionUndertakingS4Test extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new DeleteConditionUndertakingS4();
        $this->mockRepo('ConditionUndertaking', ConditionUndertakingRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            S4::class => [
                1 => m::mock(S4::class)->makePartial()
            ],
            ConditionUndertakingEntity::class => [
                m::mock(ConditionUndertakingEntity::class)->makePartial()
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(
            [
                's4' => 1
            ]
        );

        $this->repoMap['ConditionUndertaking']
            ->shouldReceive('fetchListForS4')
            ->andReturn(
                [
                    $this->references[ConditionUndertakingEntity::class]
                ]
            );

        $this->repoMap['ConditionUndertaking']
            ->shouldReceive('delete')
            ->once();

        $this->sut->handleCommand($command);
    }
}
