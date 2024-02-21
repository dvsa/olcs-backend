<?php

namespace Dvsa\Olcs\Cli\Controller;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class BatchControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): BatchController
    {
        $queryHandlerManager = $container->get('QueryHandlerManager');
        $commandHandlerManager = $container->get('CommandHandlerManager');

        return new BatchController(
            $queryHandlerManager,
            $commandHandlerManager
        );
    }
}
