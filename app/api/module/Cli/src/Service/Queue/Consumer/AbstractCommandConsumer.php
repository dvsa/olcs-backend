<?php

/**
 * Abstract Command Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Domain\Exception\NotReadyException;
use Dvsa\Olcs\Api\Domain\Exception\Exception as DomainException;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;

/**
 * Abstract Command Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractCommandConsumer extends AbstractConsumer
{
    protected $maxAttempts;

    /**
     * @var string the command to handle processing
     */
    protected $commandName = 'override_me';

    /**
     * @param QueueEntity $item
     * @return array
     */
    abstract public function getCommandData(QueueEntity $item);

    /**
     * @param QueueEntity $item
     * @return string
     */
    protected function getCommandName(QueueEntity $item)
    {
        return $this->commandName;
    }

    /**
     * Process the message item
     *
     * @param QueueEntity $item
     * @return string
     */
    public function processMessage(QueueEntity $item)
    {
        if (!empty($this->maxAttempts) && $item->getAttempts() > $this->maxAttempts) {
            return $this->failed($item, 'Maximum attempts exceeded');
        }

        $commandClass = $this->getCommandName($item);
        $commandData = $this->getCommandData($item);
        $command = $commandClass::create($commandData);

        try {
            $result = $this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($command);
        } catch (NotReadyException $e) {
            return $this->retry($item, $e->getRetryAfter());
        } catch (DomainException $e) {
            $message = !empty($e->getMessages()) ? implode(', ', $e->getMessages()) : $e->getMessage();
            return $this->failed($item, $message);
        } catch (\Exception $e) {
            return $this->failed($item, $e->getMessage());
        }

        $message = null;
        if (!empty($result->getMessages())) {
            $message = implode(', ', $result->getMessages());
        }
        return $this->success($item, $message);
    }
}
