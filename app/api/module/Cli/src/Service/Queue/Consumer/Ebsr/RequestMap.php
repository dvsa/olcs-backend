<?php

/**
 * Request Map
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\Ebsr;

use Dvsa\Olcs\Cli\Service\Queue\Consumer\AbstractCommandConsumer;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\ProcessRequestMap as Cmd;
use Zend\Serializer\Adapter\Json as ZendJson;

/**
 * Request Map
 */
class RequestMap extends AbstractCommandConsumer
{
    protected $commandName = Cmd::class;

    /**
     * @param QueueEntity $item
     * @return array
     */
    public function getCommandData(QueueEntity $item)
    {
        $json = new ZendJson();
        return array_merge($json->unserialize($item->getOptions()), ['user' => $item->getCreatedBy()->getId()]);
    }
}
