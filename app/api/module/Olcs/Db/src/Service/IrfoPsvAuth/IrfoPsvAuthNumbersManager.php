<?php

namespace Olcs\Db\Service\IrfoPsvAuth;

use Olcs\Db\Entity\IrfoPsvAuth;
use Olcs\Db\Entity\IrfoPsvAuthNumber;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class IrfoPsvAuthNumbersManager
 * @package Olcs\Db\Service\Operator
 */
class IrfoPsvAuthNumbersManager implements FactoryInterface
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
        $service = $serviceLocator->get('ServiceFactory')->getService('IrfoPsvAuthNumber');
        $this->setDataService($service);

        return $this;
    }

    public function processIrfoPsvAuthNumbers(IrfoPsvAuth $irfoPsvAuth, array $irfoPsvAuthNumbers)
    {
        $ids = $this->extractIds($irfoPsvAuth);

        $reduced = [];

        foreach ($irfoPsvAuthNumbers as $irfoPsvAuthNumber) {
            if (empty($irfoPsvAuthNumber['name'])) {
                // filter out empty values
                continue;
            }

            if (!empty($irfoPsvAuthNumber['id'])) {
                $this->getDataService()->update($irfoPsvAuthNumber['id'], $irfoPsvAuthNumber);
                $reduced[] = $irfoPsvAuthNumber['id'];
                unset($ids[$irfoPsvAuthNumber['id']]);
            } else {
                // for create id and version needs to be null
                $irfoPsvAuthNumber = array_merge(
                    $irfoPsvAuthNumber,
                    [
                        'id' => null,
                        'version' => null,
                        'irfoPsvAuth' => $irfoPsvAuth,
                    ]
                );

                $reduced[] = $this->getDataService()->create($irfoPsvAuthNumber);
            }
        }

        foreach (array_flip($ids) as $id) {
            $this->getDataService()->delete($id);
        }

        return $reduced;
    }

    /**
     * @param IrfoPsvAuth $irfoPsvAuth
     * @return array|\Doctrine\Common\Collections\Collection
     */
    protected function extractIds(IrfoPsvAuth $irfoPsvAuth)
    {
        $mapFunction = function (IrfoPsvAuthNumber $item) {
            return $item->getId();
        };

        /** @var \Doctrine\Common\Collections\Collection $collection */
        $collection = $irfoPsvAuth->getIrfoPsvAuthNumbers();
        $ids = array_flip($collection->map($mapFunction)->toArray());
        return $ids;
    }
}
