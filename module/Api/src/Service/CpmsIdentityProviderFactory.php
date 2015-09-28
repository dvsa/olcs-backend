<?php

/**
 * CPMS Identity Provider Service Factory
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Service;

use RuntimeException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * CPMS Identity Provider Service Factory
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CpmsIdentityProviderFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = new CpmsIdentityProvider();

        $config = $serviceLocator->get('Config');

        if (!isset($config['cpms_credentials'])) {
            throw new RuntimeException('Missing required CPMS configuration');
        }

        $config = $config['cpms_credentials'];

        // @todo user_id should be the authenticated user id, not from config
        if (!isset($config['user_id'])) {
            throw new RuntimeException('Missing required option cpms.user_id');
        }

        if (!isset($config['client_id'])) {
            throw new RuntimeException('Missing required option cpms.client_id');
        }

        if (!isset($config['client_secret'])) {
            throw new RuntimeException('Missing required option cpms.client_secret');
        }

        $service->setUserId($config['user_id']);
        $service->setClientId($config['client_id']);
        $service->setClientSecret($config['client_secret']);

        return $service;
    }
}
