<?php

/**
 * Abstract Handler
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareInterface;
use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareTrait;
use Olcs\Logging\Log\Logger;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\ValidatorManager;
use Dvsa\Olcs\Api\Domain\Validation\Validators;
/**
 * Abstract Handler
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 *
 * @method Validators\IsOwner isOwner($organisationProvider, $user)
 */
abstract class AbstractHandler implements
    HandlerInterface,
    AuthAwareInterface,
    FactoryInterface,
    RepositoryManagerAwareInterface
{
    use AuthAwareTrait,
        RepositoryManagerAwareTrait;

    /**
     * @var ValidatorManager
     */
    protected $validatorManager;

    /**
     * @inheritdoc
     */
    abstract public function isValid($dto);

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
        $this->getValidatorManager()->get($method);

        if ($this->getValidatorManager()->has($method) === false) {
            throw new \RuntimeException(get_class($this) . '::' . $method . ' doesn\'t exist');
        }

        $validator = $this->getValidatorManager()->get($method);

        return call_user_func_array([$validator, 'isValid'], $params);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceManager = $serviceLocator->getServiceLocator();

        $this->setAuthService($mainServiceManager->get(AuthorizationService::class));
        $this->setRepoManager($mainServiceManager->get('RepositoryServiceManager'));
        $this->setValidatorManager($mainServiceManager->get('DomainValidatorManager'));

        return $this;
    }
}
