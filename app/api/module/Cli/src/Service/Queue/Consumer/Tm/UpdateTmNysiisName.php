<?php

/**
 * Update TM name with Nysiis data
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\Tm;

use Dvsa\Olcs\Cli\Service\Queue\Consumer\AbstractCommandConsumer;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Domain\Command\Tm\UpdateNysiisName as Cmd;
use Zend\Serializer\Adapter\Json as ZendJson;
use Dvsa\Olcs\Api\Domain\Exception\NysiisException;
/**
 * Update TM name with Nysiis data
 */
class UpdateTmNysiisName extends AbstractCommandConsumer
{
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
        return array_merge(
            [
                'id' => $item->getEntityId()
            ],
            $json->unserialize($item->getOptions())
        );
    }

    /**
     * Method to handle the Service Manager exception. Default to failed.
     *
     * @param QueueEntity $item
     * @param \Exception $e
     * @return string
     */
    protected function handleZendServiceException(QueueEntity $item, \Exception $e)
    {
        $ni = new NysiisException();
        return $this->retry($item, $ni->getRetryAfter(), $e->getMessage());
    }
}
