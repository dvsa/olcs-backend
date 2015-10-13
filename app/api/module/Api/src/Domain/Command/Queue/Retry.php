<?php

/**
 * Retry queue item
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Queue;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Retry queue item
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class Retry extends AbstractCommand
{
    /**
     * @var Dvsa\Olcs\Api\Entity\Queue
     */
    protected $item;

    /**
     * @var string
     */
    protected $retryAfter;

    /**
     * @return Dvsa\Olcs\Api\Entity\Queue
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Gets the value of retryAfter.
     *
     * @return string
     */
    public function getRetryAfter()
    {
        return $this->retryAfter;
    }
}
