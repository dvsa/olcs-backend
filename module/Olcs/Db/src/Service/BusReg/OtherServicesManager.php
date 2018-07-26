<?php

namespace Olcs\Db\Service\BusReg;

use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Entity\Bus\BusRegOtherService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class OtherServicesManager
 * @package Olcs\Db\Service\BusReg
 */
class OtherServicesManager implements FactoryInterface
{
    /**
     * @var \Olcs\Db\Service\ServiceAbstract
     */
    protected $dataService;

    /**
     * @param \Olcs\Db\Service\ServiceAbstract $dataService
     */
    public function setDataService($dataService)
    {
        $this->dataService = $dataService;
    }

    /**
     * @return \Olcs\Db\Service\ServiceAbstract
     */
    public function getDataService()
    {
        return $this->dataService;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = $container->get('ServiceFactory')->getService('BusRegOtherService');
        $this->setDataService($service);

        return $this;
    }

    public function processOtherServiceNumbers(BusReg $busReg, array $otherServices)
    {
        $ids = $this->extractIds($busReg);

        $reduced = [];

        foreach ($otherServices as $serviceDetails) {
            if (isset($serviceDetails['id']) && !empty($serviceDetails['id'])) {
                $this->getDataService()->update($serviceDetails['id'], $serviceDetails);
                $reduced[] = $serviceDetails['id'];
                unset($ids[$serviceDetails['id']]);
            } else {
                $serviceDetails['busReg'] = $busReg;
                $reduced[] = $this->getDataService()->create($serviceDetails);
            }
        }

        foreach (array_flip($ids) as $id) {
            $this->getDataService()->delete($id);
        }

        return $reduced;
    }

    /**
     * @param BusReg $busReg
     * @return array|\Doctrine\Common\Collections\Collection
     */
    protected function extractIds(BusReg $busReg)
    {
        $mapFunction = function (BusRegOtherService $item) {
            return $item->getId();
        };

        /** @var \Doctrine\Common\Collections\Collection $collection */
        $collection = $busReg->getOtherServices();
        $ids = $collection->map($mapFunction);
        $ids = array_flip($ids->toArray());
        return $ids;
    }
}
