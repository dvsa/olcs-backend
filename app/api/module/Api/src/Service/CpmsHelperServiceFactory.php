<?php

/**
 * Cpms Helper Service Factory
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Cpms Helper Service Factory
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class CpmsHelperServiceFactory implements FactoryInterface
{
    /**
     * This factory wraps the CpmsHelperService creation by checking config for
     * for the version to use. Factoryception!
     *
     * @return CpmsHelperInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        // @todo check which version flag to use from config
        if (isset($config['cpms_api']['rest_client']['options']['version'])
            && $config['cpms_api']['rest_client']['options']['version'] == '2') {
            $service = new CpmsV2HelperService();
        } else {
            $service = new CpmsV1HelperService();
        }

        return $service->createService($serviceLocator);
    }
}
