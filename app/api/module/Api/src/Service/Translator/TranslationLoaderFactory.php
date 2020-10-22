<?php

namespace Dvsa\Olcs\Api\Service\Translator;

use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * TranslationLoaderFactory for API nodes
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class TranslationLoaderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TranslationLoader
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TranslationLoader
    {
        return $this($serviceLocator, TranslationLoader::class);
    }

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return TranslationLoader
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TranslationLoader
    {
        $parentLocator = $container->getServiceLocator();

        return new TranslationLoader(
            $parentLocator->get(CacheEncryption::class),
            $parentLocator->get('RepositoryServiceManager')->get('TranslationKeyText')
        );
    }
}
