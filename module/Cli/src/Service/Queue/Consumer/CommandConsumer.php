<?php

/**
 * Command Consumer
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Zend\Serializer\Adapter\Json as ZendJson;

/**
 * Command Consumer
 */
class CommandConsumer extends AbstractCommandConsumer
{
    /**
     * @param QueueEntity $item
     * @return string
     */
    protected function getCommandName(QueueEntity $item)
    {
        $json = new ZendJson();
        return $json->unserialize($item->getOptions())['commandClass'];
    }

    /**
     * @param QueueEntity $item
     * @return array
     */
    public function getCommandData(QueueEntity $item)
    {
        $json = new ZendJson();
        return $json->unserialize($item->getOptions())['commandData'];
    }
}
