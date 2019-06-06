<?php

namespace Dvsa\Olcs\Email\Transport;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class S3FileOptionsFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $s3fileOptions = new S3FileOptions($config, $serviceLocator->get('S3Client'));
        return $s3fileOptions;
    }
}
