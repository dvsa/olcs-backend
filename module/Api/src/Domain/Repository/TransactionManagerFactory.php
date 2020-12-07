<?php

/**
 * Class TransactionManager
 * @package Dvsa\Olcs\Api\Domain\Repository
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Dvsa\Olcs\Api\Domain\Exception;
use Doctrine\ORM\Query;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Stdlib\ArraySerializableInterface as QryCmd;

/**
 * Class TransactionManager
 * @package Dvsa\Olcs\Api\Domain\Repository
 */
final class TransactionManagerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return new TransactionManager($serviceLocator->get('doctrine.entitymanager.orm_default'));
    }
}
