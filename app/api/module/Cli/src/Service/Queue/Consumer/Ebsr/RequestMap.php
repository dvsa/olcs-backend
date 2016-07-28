<?php

/**
 * Request Map
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\Ebsr;

use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\ProcessRequestMap as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Entity\Task\Task as TaskEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\AbstractCommandConsumer;
use Zend\Serializer\Adapter\Json as ZendJson;

/**
 * Request Map
 */
class RequestMap extends AbstractCommandConsumer
{
    const TASK_FAIL_DESC = 'Route map generation for: %s failed';

    /**
     * @var string the command class
     */
    protected $commandName = Cmd::class;

    /**
     * @var int Max retry attempts before fails
     */
    protected $maxAttempts = 4;

    /**
     * gets command data
     *
     * @param QueueEntity $item
     * @return array
     */
    public function getCommandData(QueueEntity $item)
    {
        $json = new ZendJson();
        return array_merge($json->unserialize($item->getOptions()), ['user' => $item->getCreatedBy()->getId()]);
    }

    /**
     * Creates a task if the map request has failed, then calls the usual failure method
     *
     * @param QueueEntity $item
     * @param string|null $reason
     * @return string
     */
    public function failed(QueueEntity $item, $reason = null)
    {
        $cmdData = $this->getCommandData($item);

        $data = [
            'category' => TaskEntity::CATEGORY_BUS,
            'subCategory' => TaskEntity::SUBCATEGORY_EBSR,
            'description' => sprintf(self::TASK_FAIL_DESC, $cmdData['regNo']),
            'actionDate' => date('Y-m-d'),
            'busReg' => $cmdData['id'],
            'licence' => $cmdData['licence'],
        ];

        $command = CreateTaskCmd::create($data);

        $this->handleCommand($command);

        return parent::failed($item, $reason);
    }
}
