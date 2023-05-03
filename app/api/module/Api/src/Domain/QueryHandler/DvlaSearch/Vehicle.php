<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\DvlaSearch;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Service\DvlaSearch\Exception\BadResponseException;
use Dvsa\Olcs\Api\Service\DvlaSearch\Exception\ServiceException;
use Dvsa\Olcs\Api\Service\DvlaSearch\Exception\VehicleUnavailableException;
use Dvsa\Olcs\Api\Service\DvlaSearch\DvlaSearchService;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Transfer\Query\DvlaSearch\Vehicle as VehicleQuery;
use GuzzleHttp\Exception\GuzzleException;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Vehicle extends AbstractQueryHandler
{
    /**
     * @var DvlaSearchService
     */
    protected $dvlaSearchService;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return Vehicle
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this->__invoke($serviceLocator, Vehicle::class);
    }


    /**
     * @param VehicleQuery|QueryInterface $query
     * @return array<string,array<mixed>>
     * @throws ServiceException
     * @throws BadResponseException
     * @throws GuzzleException
     */
    public function handleQuery(QueryInterface $query)
    {
        try {
            $vehicle = $this->dvlaSearchService->getVehicle($query->getVrm());
            return [
                'count' => 1,
                'result' => [$vehicle->toArray()]
            ];
        } catch (VehicleUnavailableException $exception) {
            return [
                'count' => 0,
                'result' => []
            ];
        }
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return Vehicle
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $fullContainer = $container;
            $container = $container->getServiceLocator();
        }
        $this->dvlaSearchService = $container->get(DvlaSearchService::class);
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}
