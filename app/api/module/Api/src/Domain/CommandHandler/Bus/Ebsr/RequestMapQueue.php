<?php

/**
 * Request new Ebsr map
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Bus\Ebsr\RequestMap as RequestMapCmd;
use Dvsa\Olcs\Api\Domain\Command\Queue\Create as CreateQueue;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Zend\Serializer\Adapter\Json as ZendJson;

/**
 * Request new Ebsr map
 */
final class RequestMapQueue extends AbstractCommandHandler
{
    /**
     * @param CommandInterface $command
     * @return Result
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var RequestMapCmd $command */
        $result = new Result();

        $jsonSerializer = new ZendJson();

        $dtoData = [
            'entityId' => $command->getId(),
            'type' => Queue::TYPE_EBSR_REQUEST_MAP,
            'status' => Queue::STATUS_QUEUED,
            'options' => $jsonSerializer->serialize($command->getArrayCopy())
        ];

        $this->handleSideEffect(CreateQueue::create($dtoData));

        $result->addMessage('New map was requested');

        return $result;
    }
}
