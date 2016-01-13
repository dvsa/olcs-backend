<?php

/**
 * Failed queue item
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Queue;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Failed queue item
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class Failed extends AbstractCommand
{
    /**
     * @var Dvsa\Olcs\Api\Entity\Queue
     */
    protected $item;

    /**
     * @return Dvsa\Olcs\Api\Entity\Queue
     */
    public function getItem()
    {
        return $this->item;
    }
}
