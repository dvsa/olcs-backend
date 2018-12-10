<?php

namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer;

use Doctrine\DBAL\DBALException;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail as ContinuationDetailEntity;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\ContinuationChecklist;

/**
 * @covers \Dvsa\Olcs\Cli\Service\Queue\Consumer\ContinuationChecklist
 * @covers \Dvsa\Olcs\Cli\Service\Queue\Consumer\AbstractCommandConsumer
 */
class ContinuationChecklistTest extends AbstractConsumerTestCase
{
    protected $consumerClass = ContinuationChecklist::class;

    /** @var  ContinuationChecklist */
    protected $sut;

    public function testProcessMessageSuccess()
    {
        $user = new UserEntity('pid', 'type');
        $user->setId(1);
        $item = new QueueEntity();
        $item->setId(99);
        $item->setEntityId(69);
        $item->setCreatedBy($user);

        $expectedDtoData = ['id' => 69, 'user' => 1];
        $cmdResult = new Result();
        $cmdResult
            ->addId('continuationDetail', 69)
            ->addId('document', 101)
            ->addMessage('ContinuationDetail updated')
            ->addMessage('Document created');

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\ContinuationDetail\Process::class,
            $expectedDtoData,
            $cmdResult
        );

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Complete::class,
            ['item' => $item],
            new Result(),
            false
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Successfully processed message: 99  ContinuationDetail updated, Document created',
            $result
        );
    }

    public function testProcessMessageFailure()
    {
        $user = new UserEntity('pid', 'type');
        $user->setId(1);
        $item = new QueueEntity();
        $item->setId(99);
        $item->setEntityId(69);
        $item->setCreatedBy($user);

        $this->expectCommandException(
            \Dvsa\Olcs\Api\Domain\Command\ContinuationDetail\Process::class,
            ['id' => 69, 'user' => 1],
            \Dvsa\Olcs\Api\Domain\Exception\Exception::class,
            'epic fail'
        );

        $this->expectCommandException(
            \Dvsa\Olcs\Transfer\Command\ContinuationDetail\Update::class,
            [
                'id' => 69,
                'status' => ContinuationDetailEntity::STATUS_ERROR,
                'version' => null,
                'received' => null,
                'totAuthVehicles' => null,
                'totPsvDiscs' => null,
                'totCommunityLicences' => null,
            ],
            \Dvsa\Olcs\Api\Domain\Exception\Exception::class,
            'marking as fail failed'
        );

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Failed::class,
            [
                'item' => $item,
                'lastError' => 'epic fail, marking as fail failed',
            ],
            new Result(),
            false
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Failed to process message: 99  epic fail, marking as fail failed',
            $result
        );
    }

    public function testProcessMessageFailureOrmException()
    {
        $user = new UserEntity('pid', 'type');
        $user->setId(1);
        $item = new QueueEntity();
        $item->setId(99);
        $item->setEntityId(69);
        $item->setCreatedBy($user);

        $this->expectCommandException(
            \Dvsa\Olcs\Api\Domain\Command\ContinuationDetail\Process::class,
            ['id' => 69, 'user' => 1],
            \Doctrine\ORM\ORMException::class,
            'ORM Exception'
        );

        $this->expectException(\Doctrine\ORM\ORMException::class, 'ORM Exception');

        $this->sut->processMessage($item);
    }

    public function testProcessMessageFailureDbalException()
    {
        $user = new UserEntity('pid', 'type');
        $user->setId(1);
        $item = new QueueEntity();
        $item->setId(99);
        $item->setEntityId(69);
        $item->setCreatedBy($user);

        $this->expectCommandException(
            \Dvsa\Olcs\Api\Domain\Command\ContinuationDetail\Process::class,
            ['id' => 69, 'user' => 1],
            DBALException::class,
            'DBAL Exception'
        );

        $this->expectException(DBALException::class, 'DBAL Exception');

        $this->sut->processMessage($item);
    }
}
