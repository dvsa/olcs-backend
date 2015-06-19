<?php

/**
 * Delete ConditionUndertaking Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\ConditionUndertaking;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\ConditionUndertaking\DeleteConditionUndertaking;
use Dvsa\Olcs\Api\Domain\Repository\ConditionUndertaking;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Cases\ConditionUndertaking\DeleteConditionUndertaking as Cmd;
use Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking as ConditionUndertakingEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;

/**
 * Delete ConditionUndertaking Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class DeleteConditionUndertakingTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeleteConditionUndertaking();
        $this->mockRepo('ConditionUndertaking', ConditionUndertaking::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Cmd::Create(
            [
                'id' => 99,
                'version' => 1
            ]
        );

        /** @var ConditionUndertakingEntity $conditionUndertaking */
        $conditionUndertaking = m::mock(ConditionUndertakingEntity::class)->makePartial();
        $conditionUndertaking->setId($command->getId());

        $this->repoMap['ConditionUndertaking']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $command->getVersion())
            ->andReturn($conditionUndertaking)
            ->once()
            ->shouldReceive('delete')
            ->with(m::type(ConditionUndertakingEntity::class))
            ->andReturnUsing(
                function (ConditionUndertakingEntity $conditionUndertaking) use (&$conditionUndertaking) {
                    $conditionUndertaking->setId(99);
                }
            )
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('ConditionUndertaking deleted', $result->getMessages());
    }
}
