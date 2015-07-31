<?php

/**
 * Abstract Companies House Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\CompaniesHouse;

use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Domain\Exception\Exception as DomainException;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\AbstractConsumer as GenericAbstractConsumer;

/**
 * Abstract Companies House Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractConsumer extends GenericAbstractConsumer
{
    /**
     * @var string the command to handle processing
     */
    protected $commandName = 'override_me';

    /**
     * Process the message item
     *
     * @param QueueEntity $item
     * @return string
     */
    public function processMessage(QueueEntity $item)
    {
        $options = (array) json_decode($item->getOptions());

        $commandClass = $this->commandName;
        $command = $commandClass::create(['companyNumber' => $options['companyNumber']]);

        try {
            $result = $this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($command);
        } catch (DomainException $e) {
            $message = !empty($e->getMessages()) ? $e->getMessages()[0] : $e->getMessage();
            return $this->failed($item, $message);
        } catch (\Exception $e) {
            return $this->failed($item, $e->getMessage());
        }

        $message = null;
        if (!empty($result->getMessages())) {
            $message = $result->getMessages()[0];
        }
        return $this->success($item, $message);
    }
}
