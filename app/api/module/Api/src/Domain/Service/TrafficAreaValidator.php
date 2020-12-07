<?php

namespace Dvsa\Olcs\Api\Domain\Service;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * TrafficAreaValidator
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TrafficAreaValidator implements \Laminas\ServiceManager\FactoryInterface
{
    const ERR_TA_GOODS = 'ERR_TA_GOODS';   // Operator already has Goods licence/application in same Traffic Area
    const ERR_TA_PSV = 'ERR_TA_PSV';       // Operator already has PSV licence/application in same Traffic Area
    const ERR_TA_PSV_SR = 'ERR_TA_PSV_SR'; // Operator already has PSV SR licence/application in same Traffic Area
    const ERR_TA_PSV_RES = 'ERR_TA_PSV_RES'; // Operator already has PSV Restricted licence/application in same Traffic Area

    protected $messages = [];

    /**
     * @var Address
     */
    protected $addressService;

    /**
     * @var AdminAreaTrafficArea
     */
    protected $adminAreaTrafficAreaRepo;

    public function createService(\Laminas\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->addressService = $serviceLocator->get('AddressService');
        $this->adminAreaTrafficAreaRepo = $serviceLocator->get('RepositoryServiceManager')->get('AdminAreaTrafficArea');

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
                $errorCode = $this->licenceConflictError($application, $orgLicence);
                if ($errorCode) {
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
                    $errorCode = $this->applicationConflictError($application, $orgApplication);
                    if ($errorCode) {
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
     * @param Application $application         the ongoing application
     * @param Application $existingApplication with the same traffic area of the ongoing application
     * @return bool|string
     */
    private function applicationConflictError(Application $application, Application $existingApplication)
    {
        // if its a new goods application and there is an
        // existing application (not variation) with the same traffic area
        if ($application->isGoods() &&
            $existingApplication->isGoods()
        ) {
            return self::ERR_TA_GOODS;
        }

        // if its a new PSV application (excluding special restricted)
        // and there is an existing PSV application (not variation) with the same traffic area
        if ($application->isPsv() &&
            !$application->isSpecialRestricted() &&
            $existingApplication->isPsv() &&
            !$existingApplication->isSpecialRestricted()
        ) {
            return self::ERR_TA_PSV;
        }

        // if its a new PSV special restricted application
        // and there is an existing Special restricted PSV application (not variation) with the same traffic area
        if ($application->isPsv() &&
            $application->isSpecialRestricted() &&
            $existingApplication->isPsv() &&
            $existingApplication->isSpecialRestricted()
        ) {
            return self::ERR_TA_PSV_SR;
        }

        return false;
    }

    /**
     * @param Application $application the ongoing application
     * @param Licence     $licence     with the same traffic area of the ongoing application
     * @return bool|string
     */
    private function licenceConflictError(Application $application, Licence $licence)
    {
        // if its a new goods application and there is an 'active'
        // goods licence with the same traffic area
        if ($application->isGoods() &&
            $licence->isGoods()
        ) {
            return self::ERR_TA_GOODS;
        }

        // if its a new PSV application (excluding restricted and special restricted)
        // and there is an 'active' PSV licence with the same traffic area
        if ($application->isPsv() &&
            !$application->isSpecialRestricted() &&
            !$application->isRestricted() &&
            $licence->isPsv() &&
            !$licence->isSpecialRestricted() &&
            !$licence->isRestricted()
        ) {
            return self::ERR_TA_PSV;
        }

        // if its a new PSV special restricted application and there is an 'active'
        // PSV Special restricted licence
        if ($application->isPsv() &&
            $application->isSpecialRestricted() &&
            $licence->isPsv() &&
            $licence->isSpecialRestricted()
        ) {
            return self::ERR_TA_PSV_SR;
        }

        // if its a new PSV special restricted application
        // and there is an 'active' PSV Restricted licence
        if ($application->isPsv() &&
            $application->isRestricted() &&
            $licence->isPsv() &&
            $licence->isRestricted()
        ) {
            return self::ERR_TA_PSV_RES;
        }

        return false;
    }
}
