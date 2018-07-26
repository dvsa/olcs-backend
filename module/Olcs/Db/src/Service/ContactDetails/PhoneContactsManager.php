<?php

namespace Olcs\Db\Service\ContactDetails;

use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\ContactDetails\PhoneContact;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class PhoneContactsManager
 * @package Olcs\Db\Service\ContactDetails
 */
class PhoneContactsManager implements FactoryInterface
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
        $service = $container->get('ServiceFactory')->getService('ContactDetailsPhoneContacts');
        $this->setDataService($service);

        return $this;
    }

    public function processPhoneContacts(ContactDetails $contactDetails, array $phoneContacts)
    {
        $ids = $this->extractIds($contactDetails);

        $reduced = [];

        foreach ($phoneContacts as $phoneContact) {
            if (isset($contactDetails['id']) && !empty($contactDetails['id'])) {
                $this->getDataService()->update($contactDetails['id'], $contactDetails);
                $reduced[] = $contactDetails['id'];
                unset($ids[$contactDetails['id']]);
            } else {
                $phoneContact['contactDetails'] = $contactDetails;
                $reduced[] = $this->getDataService()->create($phoneContact);
            }
        }

        foreach (array_flip($ids) as $id) {
            $this->getDataService()->delete($id);
        }

        return $reduced;
    }

    /**
     * @param ContactDetails $contactDetails
     * @return array|\Doctrine\Common\Collections\Collection
     */
    protected function extractIds(ContactDetails $contactDetails)
    {
        $mapFunction = function (PhoneContact $item) {
            return $item->getId();
        };

        /** @var \Doctrine\Common\Collections\Collection $collection */
        $collection = $contactDetails->getPhoneContacts();
        $ids = $collection->map($mapFunction);
        $ids = array_flip($ids->toArray());
        return $ids;
    }
}
