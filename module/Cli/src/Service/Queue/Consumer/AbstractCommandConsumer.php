<?php

namespace Dvsa\Olcs\Cli\Service\Queue\Consumer;

use Doctrine\DBAL\DBALException;
use Dvsa\Olcs\Api\Domain\Exception\Exception as DomainException;
use Dvsa\Olcs\Api\Domain\Exception\NotReadyException;
use Dvsa\Olcs\Api\Domain\Exception\NysiisException;
use Dvsa\Olcs\Api\Domain\Exception\TransxchangeException;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Email\Exception\EmailNotSentException;
use Zend\ServiceManager\Exception\ExceptionInterface as ZendServiceException;

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
    protected $maxAttempts = 100;

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
            return $this->failed($item, QueueEntity::ERR_MAX_ATTEMPTS);
        }

        $commandClass = $this->getCommandName($item);
        $commandData = $this->getCommandData($item);
        $command = $commandClass::create($commandData);

        try {
            $result = $this->handleCommand($command);
        } catch (NotReadyException $e) {
            return $this->retry($item, $e->getRetryAfter(), $e->getMessage());
        } catch (EmailNotSentException $e) {
            return $this->retry($item, $this->retryAfter, $e->getMessage());
        } catch (NysiisException $e) {
            return $this->retry($item, $e->getRetryAfter(), $e->getMessage());
        } catch (DomainException $e) {
            $message = !empty($e->getMessages()) ? implode(', ', $e->getMessages()) : $e->getMessage();
            return $this->failed($item, $message);
        } catch (ZendServiceException $e) {
            return $this->handleZendServiceException($item, $e);
        } catch (\Doctrine\ORM\ORMException $e) {
            // rethrow ORMException such as Entity Manager Closed.
            throw $e;
        } catch (DBALException $e) {
            // rethrow any exception from DBAL
            throw $e;
        } catch (\Exception $e) {
            return $this->failed($item, $e->getMessage());
        }

        $message = null;
        if (!empty($result->getMessages())) {
            $message = implode(', ', $result->getMessages());
        }
        return $this->success($item, $message);
    }

    /**
     * Method to handle the Service Manager exception. Default to failed.
     *
     * @param QueueEntity $item queue item
     * @param \Exception  $e    exception
     *
     * @return string
     */
    protected function handleZendServiceException(QueueEntity $item, \Exception $e)
    {
        return $this->failed($item, $e->getMessage());
    }
}
