<?php

/**
 * Validation Helper Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\ValidatorManager;

/**
 * Validation Helper Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 *
 * @method Validators\IsOwner isOwner($organisationProvider)
 * @method Validators\DoesOwnLicence doesOwnLicence($entityId)
 * @method Validators\DoesOwnApplication doesOwnApplication($entityId)
 * @method Validators\DoesOwnCompanySubsidiary doesOwnCompanySubsidiary($entityId)
 * @method Validators\DoesOwnOrganisation doesOwnOrganisation($entityId)
 * @method Validators\DoesOwnOrganisationPerson doesOwnOrganisationPerson($entityId)
 * @method Validators\DoesOwnPerson doesOwnPerson($entityId)
 * @method Validators\CanAccessLicence canAccessLicence($entityId)
 * @method Validators\CanAccessApplication canAccessApplication($entityId)
 * @method Validators\CanAccessApplicationOperatingCentre canAccessApplicationOperatingCentre($entityId)
 * @method Validators\CanAccessLicenceOperatingCentre canAccessLicenceOperatingCentre($entityId)
 * @method Validators\CanAccessCompanySubsidiary canAccessCompanySubsidiary($entityId)
 * @method Validators\CanAccessOrganisation canAccessOrganisation($entityId)
 * @method Validators\CanAccessOrganisationPerson canAccessOrganisationPerson($entityId)
 * @method Validators\CanAccessTransportManagerApplication canAccessTransportManagerApplication($entityId)
 * @method Validators\CanAccessPreviousConviction canAccessPreviousConviction($entityId)
 * @method Validators\CanAccessTrailer canAccessTrailer($entityId)
 * @method Validators\CanAccessPerson canAccessPerson($entityId)
 * @method Validators\CanAccessPsvDisc canAccessPsvDiscs($entityId)
 * @method Validators\CanAccessTransportManagerLicence canAccessTransportManagerLicence($entityId)
 * @method Validators\CanAccessUser canAccessUser($entityId)
 * @method Validators\CanAccessLicenceVehicle canAccessLicenceVehicle($entityId)
 * @method Validators\CanAccessCorrespondenceInbox canAccessCorrespondenceInbox($entityId)
 * @method Validators\CanAccessDocument canAccessDocument($entityId)
 */
trait ValidationHelperTrait
{
    /**
     * @var ValidatorManager
     */
    protected $validatorManager;

    /**
     * @return ValidatorManager
     */
    public function getValidatorManager()
    {
        return $this->validatorManager;
    }

    /**
     * @param ValidatorManager $validatorManager
     */
    public function setValidatorManager(ValidatorManager $validatorManager)
    {
        $this->validatorManager = $validatorManager;
    }

    public function __call($method, $params)
    {
        if ($this->getValidatorManager()->has($method) === false) {
            throw new \RuntimeException(get_class($this) . '::' . $method . ' doesn\'t exist');
        }

        $validator = $this->getValidatorManager()->get($method);

        return call_user_func_array([$validator, 'isValid'], $params);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceManager = $serviceLocator->getServiceLocator();

        if ($this instanceof AuthAwareInterface) {
            $this->setAuthService($mainServiceManager->get(AuthorizationService::class));
        }

        if ($this instanceof RepositoryManagerAwareInterface) {
            $this->setRepoManager($mainServiceManager->get('RepositoryServiceManager'));
        }

        $this->setValidatorManager($mainServiceManager->get('DomainValidatorManager'));

        return $this;
    }
}
