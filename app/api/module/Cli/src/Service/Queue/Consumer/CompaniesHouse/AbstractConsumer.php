<?php

/**
 * Abstract Companies House Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\CompaniesHouse;

use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\AbstractCommandConsumer;

/**
 * Abstract Companies House Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractConsumer extends AbstractCommandConsumer
{
    /**
     * @param QueueEntity $item
     * @return array
     */
    public function getCommandData(QueueEntity $item)
    {
        $options = (array) json_decode($item->getOptions());

        return [
            'companyNumber' => $options['companyNumber']
        ];
    }
}
