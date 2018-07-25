<?php

namespace Dvsa\Olcs\Api\Domain\Service;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * TrafficAreaValidator
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TrafficAreaValidator implements FactoryInterface
{
    const ERR_TA_GOODS = 'ERR_TA_GOODS';   // Operator already has Goods licence/application in same Traffic Area
    const ERR_TA_PSV = 'ERR_TA_PSV';       // Operator already has PSV licence/application in same Traffic Area
    const ERR_TA_PSV_SR = 'ERR_TA_PSV_SR'; // Operator already has PSV SR licence/application in same Traffic Area

    protected $messages = [];

    /**
     * @var Address
     */
    protected $addressService;

    /**
     * @var AdminAreaTrafficArea
     */
    protected $adminAreaTrafficAreaRepo;

    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->addressService = $container->get('AddressService');
        $this->adminAreaTrafficAreaRepo = $container->get('RepositoryServiceManager')->get('AdminAreaTrafficArea');

        return $this;
    }

    /**
     * Check that the operator does not have other licences/application in the same traffic area
     *
     * @param Application $application
     * @param string $postcode
     *
     * @return true|array
     */
    public function validateForSameTrafficAreasWithPostcode(Application $application, $postcode)
    {
        $trafficArea = $this->addressService->fetchTrafficAreaByPostcode($postcode, $this->adminAreaTrafficAreaRepo);
        if ($trafficArea === null) {
            return true;
        }

        return $this->validateForSameTrafficAreas($application, $trafficArea->getId());
    }

    /**
     * Check that the operator does not have other licences/application in the same traffic area
     *
     * @param Application $application
     * @param string      $trafficAreaId
     *
     * @return true|array
     */
    public function validateForSameTrafficAreas(Application $application, $trafficAreaId)
    {
        $validLicenceStatuses = [
            Licence::LICENCE_STATUS_VALID,
            Licence::LICENCE_STATUS_SUSPENDED,
            Licence::LICENCE_STATUS_CURTAILED,
        ];

        $validApplicationStatuses = [
            Application::APPLICATION_STATUS_NOT_SUBMITTED,
            Application::APPLICATION_STATUS_UNDER_CONSIDERATION,
            Application::APPLICATION_STATUS_GRANTED,
        ];

        // iterate over each licence this applications organisation owns
        foreach ($application->getLicence()->getOrganisation()->getLicences() as $orgLicence) {
            /* @var $orgLicence \Dvsa\Olcs\Api\Entity\Licence\Licence */

            // if orgLicence is not same as this application licence AND
            // orgLicence is Valid, suspended or curtailed AND
            // orgLicence does not have a queued revocation rule AND
            // orgLicence is same trafiic area
            if ($orgLicence !== $application->getLicence() &&
                in_array($orgLicence->getStatus()->getId(), $validLicenceStatuses) &&
                !$orgLicence->hasQueuedRevocation() &&
                $orgLicence->getTrafficArea()->getId() === $trafficAreaId
            ) {
                if ($errorCode = $this->checkLicenceType($application, $orgLicence)) {
                    return $this->getResponse($errorCode, $orgLicence->getTrafficArea()->getName());
                }
            }

            // iterate over each application this applications organisation owns
            foreach ($orgLicence->getApplications() as $orgApplication) {
                /* @var $orgApplication Application */

                // If orgApplication is not this application AND
                // orgApplication is Not submitted, under consideration or granted AND
                // orgApplication is a new application AND
                // orgApplication is same trafiic area
                if ($orgApplication !== $application &&
                    in_array($orgApplication->getStatus()->getId(), $validApplicationStatuses) &&
                    $orgApplication->isNew() &&
                    $orgApplication->getTrafficArea() &&
                    $orgApplication->getTrafficArea()->getId() === $trafficAreaId
                ) {
                    if ($errorCode = $this->checkLicenceType($application, $orgApplication)) {
                        return $this->getResponse($errorCode, $orgApplication->getTrafficArea()->getName());
                    }
                }
            }
        }

        return true;
    }

    /**
     * Get the structured response
     *
     * @param string $errorCode
     * @param string $trafficAreaName
     *
     * @return array
     */
    private function getResponse($errorCode, $trafficAreaName)
    {
        return [$errorCode => $trafficAreaName];
    }

    /**
     * @param Application $application
     * @param type $applicationOrLicence
     * @return boolean
     */
    private function checkLicenceType(Application $application, $applicationOrLicence)
    {
        // if its a new goods application and there is an 'active'
        // goods licence or new application with the same traffic area
        if ($application->isGoods() &&
            $applicationOrLicence->isGoods()
        ) {
            return self::ERR_TA_GOODS;
        }

        // if its a new PSV application (excluding special restricted) and there is an 'active'
        // PSV licence or new application with the same traffic area
        if ($application->isPsv() &&
            !$application->isSpecialRestricted() &&
            $applicationOrLicence->isPsv() &&
            !$applicationOrLicence->isSpecialRestricted()
        ) {
            return self::ERR_TA_PSV;
        }

        // if its a new PSV special restricted application and there is an 'active'
        // PSV Special restricted licence or new application
        if ($application->isPsv() &&
            $application->isSpecialRestricted() &&
            $applicationOrLicence->isPsv() &&
            $applicationOrLicence->isSpecialRestricted()
        ) {
            return self::ERR_TA_PSV_SR;
        }

        return false;
    }
}
