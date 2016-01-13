<?php

/**
 * Command Handler Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\AbstractServiceManagerFactory;

/**
 * Command Handler Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommandHandlerManagerFactory extends AbstractServiceManagerFactory
{
    const CONFIG_KEY = 'command_handlers';

    protected $serviceManagerClass = \Dvsa\Olcs\Api\Domain\CommandHandlerManager::class;
}
