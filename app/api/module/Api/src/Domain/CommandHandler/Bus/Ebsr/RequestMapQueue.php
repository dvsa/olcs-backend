<?php

/**
 * Request new Ebsr map
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClient;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Bus\Ebsr\RequestMap as RequestMapCmd;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;

/**
 * Request new Ebsr map
 */
final class RequestMapQueue extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;
    use QueueAwareTrait;

    protected $repoServiceName = 'Bus';

    /**
     * Command to queue an EBSR map request
     *
     * @param CommandInterface $command
     *
     * @return Result
     * @throws NotFoundException
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var BusReg $busReg
         * @var RequestMapCmd $command
         */
        $busReg = $this->getRepo()->fetchUsingId($command);

        if ($busReg->getEbsrSubmissions()->isEmpty()) {
            throw new NotFoundException('The specified bus registration doesn\'t have an EBSR file');
        }

        $result = new Result();

        $entityId = $command->getId();

        $optionData = [
            'scale' => $command->getScale(),
            'id' => $entityId,
            'regNo' => $busReg->getRegNo(),
            'licence' => $busReg->getLicence()->getId(),
            'user' => $this->getCurrentUser()->getId()
        ];

        $mapTemplate = ['template' => TransExchangeClient::REQUEST_MAP_TEMPLATE];
        $timetableTemplate = ['template' => TransExchangeClient::TIMETABLE_TEMPLATE];
        $recordTemplate = ['template' => TransExchangeClient::DVSA_RECORD_TEMPLATE];

        $this->createQueue($entityId, Queue::TYPE_EBSR_REQUEST_MAP, $optionData);

        $this->handleSideEffects(
            [
                $this->createQueue($entityId, Queue::TYPE_EBSR_REQUEST_MAP, $optionData + $mapTemplate),
                $this->createQueue($entityId, Queue::TYPE_EBSR_REQUEST_MAP, $optionData + $timetableTemplate),
                $this->createQueue($entityId, Queue::TYPE_EBSR_REQUEST_MAP, $optionData + $recordTemplate)
            ]
        );

        $result->addMessage('New map was requested');

        return $result;
    }
}
