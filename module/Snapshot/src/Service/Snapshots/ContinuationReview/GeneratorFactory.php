<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview;

use Dvsa\Olcs\Snapshot\Service\Snapshots\AbstractGeneratorServices;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class GeneratorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Generator(
            $container->get(AbstractGeneratorServices::class),
            $container->get('SectionAccessService'),
            $container->get('Utils\NiTextTranslation'),
            $container
        );
    }
}
