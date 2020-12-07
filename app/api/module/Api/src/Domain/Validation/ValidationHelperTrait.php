<?php

/**
 * Validation Helper Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\Olcs\Api\Domain\Validation;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\ValidatorManager;

/**
 * Validation Helper Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 *
 * @method bool isOwner($organisationProvider)
 * @method bool doesOwnLicence($entityId)
 * @method bool doesOwnApplication($entityId)
 * @method bool doesOwnCompanySubsidiary($entityId)
 * @method bool doesOwnOrganisation($entityId)
 * @method bool doesOwnOrganisationPerson($entityId)
 * @method bool doesOwnPerson($entityId)
 * @method bool canAccessLicence($entityId)
 * @method bool canAccessApplication($entityId)
 * @method bool canAccessApplicationOperatingCentre($entityId)
 * @method bool canAccessLicenceOperatingCentre($entityId)
 * @method bool canAccessCompanySubsidiary($entityId)
 * @method bool canAccessOrganisation($entityId)
 * @method bool canAccessOrganisationPerson($entityId)
 * @method bool canAccessTransportManagerApplication($entityId)
 * @method bool canAccessPreviousConviction($entityId)
 * @method bool canAccessTrailer($entityId)
 * @method bool canAccessPerson($entityId)
 * @method bool canAccessPsvDiscs($entityId)
 * @method bool canAccessTransportManagerLicence($entityId)
 * @method bool canAccessUser($entityId)
 * @method bool canAccessLicenceVehicle($entityId)
 * @method bool canAccessCorrespondenceInbox($entityId)
 * @method bool canAccessDocument($entityId)
 * @method bool canAccessSubmission($entityId)
 * @method bool canAccessCase($entityId)
 * @method bool canAccessTransportManager($entityId)
 * @method bool canAccessTmEmployment($entityId)
 * @method bool canAccessOperatingCentre($entityId)
 * @method bool canAccessBusReg($entityId)
 * @method bool canAccessStatement($entityId)
 * @method bool canAccessTransaction($transactionReference)
 * @method bool canAccessFee($feeId)
 * @method bool canAccessEbsrSubmission($entityId)
 * @method bool canAccessTxcInbox($entityId)
 * @method bool canUpdateTxcInbox($entityId)
 * @method bool canUploadEbsr($entityId)
 * @method bool canManageUser($entityId)
 * @method bool canReadUser($entityId)
 * @method bool canEditLicence($entityId)
 * @method bool canEditApplication($entityId)
 * @method bool canEditBusReg($entityId)
 * @method bool canAccessContinuationDetail($entityId)
 * @method bool isLicenceSurrenderable($licenceId)
 * @method bool canAccessIrhpApplicationWithId($entityId)
 * @method bool canEditIrhpApplicationWithId($entityId)
 * @method bool canDeleteSurrender($entityId)
 * @method bool CanAccessSurrenderedLicence($entity)
 */
trait ValidationHelperTrait
{
    /**
     * @var ValidatorManager
     */
    protected $validatorManager;

    /**
     * Get ValidatorManager
     *
     * @return ValidatorManager
     */
    public function getValidatorManager()
    {
        return $this->validatorManager;
    }

    /**
     * Set ValidatorManager
     *
     * @param ValidatorManager $validatorManager Validator manager
     *
     * @return void
     */
    public function setValidatorManager(ValidatorManager $validatorManager)
    {
        $this->validatorManager = $validatorManager;
    }

    /**
     * Call
     *
     * @param string $method Method
     * @param array  $params Params
     *
     * @return mixed
     */
    public function __call($method, $params)
    {
        if ($this->getValidatorManager()->has($method) === false) {
            throw new \RuntimeException(get_class($this) . '::' . $method . ' doesn\'t exist');
        }

        $validator = $this->getValidatorManager()->get($method);

        return call_user_func_array([$validator, 'isValid'], $params);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service locator
     *
     * @return $this
     */
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
