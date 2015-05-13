<?php

/**
 * Command Handler Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * Command Handler Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface CommandHandlerInterface
{
    public function handleCommand(ArraySerializableInterface $command);
}
