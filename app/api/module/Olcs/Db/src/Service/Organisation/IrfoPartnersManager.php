<?php

namespace Olcs\Db\Service\Organisation;

use Olcs\Db\Entity\Organisation;
use Olcs\Db\Entity\IrfoPartner;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class IrfoPartnersManager
 * @package Olcs\Db\Service\Operator
 */
class IrfoPartnersManager implements FactoryInterface
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
        $service = $serviceLocator->get('ServiceFactory')->getService('IrfoPartner');
        $this->setDataService($service);

        return $this;
    }

    public function processIrfoPartners(Organisation $organisation, array $irfoPartners)
    {
        $ids = $this->extractIds($organisation);

        $reduced = [];

        foreach ($irfoPartners as $irfoPartner) {
            if (empty($irfoPartner['name'])) {
                // filter out empty values
                continue;
            }

            if (!empty($irfoPartner['id'])) {
                $this->getDataService()->update($irfoPartner['id'], $irfoPartner);
                $reduced[] = $irfoPartner['id'];
                unset($ids[$irfoPartner['id']]);
            } else {
                // for create id and version needs to be null
                $irfoPartner = array_merge(
                    $irfoPartner,
                    [
                        'id' => null,
                        'version' => null,
                        'organisation' => $organisation,
                    ]
                );

                $reduced[] = $this->getDataService()->create($irfoPartner);
            }
        }

        foreach (array_flip($ids) as $id) {
            $this->getDataService()->delete($id);
        }

        return $reduced;
    }

    /**
     * @param Organisation $organisation
     * @return array|\Doctrine\Common\Collections\Collection
     */
    protected function extractIds(Organisation $organisation)
    {
        $mapFunction = function (IrfoPartner $item) {
            return $item->getId();
        };

        /** @var \Doctrine\Common\Collections\Collection $collection */
        $collection = $organisation->getIrfoPartners();
        $ids = array_flip($collection->map($mapFunction)->toArray());
        return $ids;
    }
}
