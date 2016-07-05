<?php

namespace Dvsa\Olcs\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Domain\Exception\Exception as DomainException;
use Dvsa\Olcs\Api\Domain\Exception\NotReadyException;
use Dvsa\Olcs\Api\Domain\Exception\NysiisException;
use Dvsa\Olcs\Api\Domain\Exception\TransxchangeException;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Email\Exception\EmailNotSentException;

/**
 * Abstract Command Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractCommandConsumer extends AbstractConsumer
{
    /**
     * @var int Max retry attempts before fails
     */
    protected $maxAttempts;

    /**
     * @var int Retry internal in seconds
     */
    protected $retryAfter = 900;

    /**
     * @var string the command to handle processing
     */
    protected $commandName = 'override_me';

    /**
     * Gets the command data
     *
     * @param QueueEntity $item the queue item
     *
     * @return array
     */
    abstract public function getCommandData(QueueEntity $item);

    /**
     * Gets the command name
     *
     * @param QueueEntity $item the queue item
     *
     * @return string
     */
    protected function getCommandName(QueueEntity $item)
    {
        return $this->commandName;
    }

    /**
     * Process the message item
     *
     * @param QueueEntity $item the queue item
     *
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
            // @todo These commands should be validated, see OLCS-13145
            // Temporarily treat them as side effects, which aren't validated
            // As the Send class allows any command to be injected, we currently don't have the correct
            // validators setup as we don;t know which commands it could run
            $result = $this->handleSideEffectCommand($command);
        } catch (NotReadyException $e) {
            return $this->retry($item, $e->getRetryAfter(), $e->getMessage());
        } catch (EmailNotSentException $e) {
            return $this->retry($item, $this->retryAfter, $e->getMessage());
        } catch (TransxchangeException $e) {
            return $this->retry($item, $this->retryAfter, $e->getMessage());
        } catch (NysiisException $e) {
            return $this->retry($item, $e->getRetryAfter(), $e->getMessage());
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
