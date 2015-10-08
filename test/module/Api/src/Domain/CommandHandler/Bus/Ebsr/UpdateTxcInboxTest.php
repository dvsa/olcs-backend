<?php

/**
 * Update TxcInbox Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Bus\Ebsr;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr\UpdateTxcInbox;
use Dvsa\Olcs\Api\Domain\Repository\TxcInbox as TxcInboxRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Bus\Ebsr\UpdateTxcInbox as Cmd;
use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox as TxcInboxEntity;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Update TxcInbox Test
 */
class UpdateTxcInboxTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateTxcInbox();
        $this->mockRepo('TxcInbox', TxcInboxRepo::class);

        parent::setUp();
    }

    /**
     * testHandleCommand
     */
    public function testHandleCommand()
    {
        $id = 99;

        $command = Cmd::Create(
            [
                'ids' => [$id]
            ]
        );

        /** @var TxcInboxEntity $txcInbox */
        $txcInbox = m::mock(TxcInboxEntity::class);
        $txcInbox->shouldReceive('setFileRead')
            ->with('Y')
            ->once()
            ->shouldReceive('getId')
            ->andReturn($id);

        $this->repoMap['TxcInbox']->shouldReceive('fetchByIds')
            ->with($command->getIds(), Query::HYDRATE_OBJECT)
            ->andReturn([$txcInbox])
            ->shouldReceive('save')
            ->with(m::type(TxcInboxEntity::class))
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }
}
