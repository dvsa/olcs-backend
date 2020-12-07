<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\DvlaSearch;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\DvlaSearch\Exception\BadResponseException;
use Dvsa\Olcs\DvlaSearch\Exception\ServiceException;
use Dvsa\Olcs\DvlaSearch\Exception\VehicleUnavailableException;
use Dvsa\Olcs\DvlaSearch\Service\Client as DvlaSearchService;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Transfer\Query\DvlaSearch\Vehicle as VehicleQuery;
use GuzzleHttp\Exception\GuzzleException;
use Laminas\ServiceManager\ServiceLocatorInterface;

class Vehicle extends AbstractQueryHandler
{
    /**
     * @var DvlaSearchService
     */
    protected $dvlaSearchService;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();
        $this->dvlaSearchService = $mainServiceLocator->get(DvlaSearchService::class);
        return parent::createService($serviceLocator);
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
}
