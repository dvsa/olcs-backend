<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Domain\Repository\ReadonlyRepositoryInterface;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;

class RepositoryServiceManager extends AbstractPluginManager
{
    const VALIDATE_ERROR = 'Plugin manager "%s" expected an instance of type RepositoryInterface or 
    ReadonlyRepositoryInterface, but "%s" was received';

    public function validate($instance)
    {
        if ($instance instanceof RepositoryInterface || $instance instanceof ReadonlyRepositoryInterface) {
            return;
        }

        throw new InvalidServiceException(sprintf(
            self::VALIDATE_ERROR,
            self::class,
            is_object($instance) ? get_class($instance) : gettype($instance)
        ));
    }
}
