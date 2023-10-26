<?php

declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class PrintJobFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PrintJob
    {
        return new PrintJob(
            $container->get('Config'),
            $container->get('FileUploader'),
            $container->get('ConvertToPdf')
        );
    }
}
