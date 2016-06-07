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
use Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClient;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Bus\Ebsr\RequestMap as RequestMapCmd;
use Dvsa\Olcs\Api\Domain\Command\Queue\Create as CreateQueue;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Zend\Serializer\Adapter\Json as ZendJson;

/**
 * Request new Ebsr map
 */
final class RequestMapQueue extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

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

        $jsonSerializer = new ZendJson();

        $optionData = [
            'scale' => $command->getScale(),
            'id' => $command->getId(),
            'regNo' => $busReg->getRegNo(),
            'licence' => $busReg->getLicence()->getId(),
            'user' => $this->getCurrentUser()->getId(),
            'template' => TransExchangeClient::REQUEST_MAP_TEMPLATE
        ];

        $dtoData = [
            'entityId' => $command->getId(),
            'type' => Queue::TYPE_EBSR_REQUEST_MAP,
            'status' => Queue::STATUS_QUEUED,
            'options' => $jsonSerializer->serialize($optionData)
        ];

        $this->handleSideEffect(CreateQueue::create($dtoData));

        $result->addMessage('New map was requested');

        return $result;
    }
}
