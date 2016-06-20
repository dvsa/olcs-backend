<?php

/**
 * Abstract Query Handler
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler;

use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\NationalRegisterAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Service\OpenAm\UserInterface;

/**
 * Abstract Query Handler
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractQueryHandler implements QueryHandlerInterface, FactoryInterface
{
    /**
     * The name of the default repo
     */
    protected $repoServiceName;

    /**
     * Tell the factory which repositories to lazy load
     */
    protected $extraRepos = [];

    /**
     * Store the instantiated repos
     *
     * @var RepositoryInterface[]
     */
    private $repos = [];

    /**
     * @var \Dvsa\Olcs\Api\Domain\QueryHandlerManager
     */
    private $queryHandler;

    private $repoManager;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        if ($this instanceof AuthAwareInterface) {
            $this->setAuthService($mainServiceLocator->get(AuthorizationService::class));
        }

        if ($this instanceof UploaderAwareInterface) {
            $this->setUploader($mainServiceLocator->get('FileUploader'));
        }

        if ($this instanceof \Dvsa\Olcs\Api\Domain\CpmsAwareInterface) {
            $this->setCpmsService($mainServiceLocator->get('CpmsHelperService'));
        }

        if ($this instanceof \Dvsa\Olcs\Api\Domain\CompaniesHouseAwareInterface) {
            $this->setCompaniesHouseService($mainServiceLocator->get('CompaniesHouseService'));
        }

        if ($this instanceof \Dvsa\Olcs\Address\Service\AddressServiceAwareInterface) {
            $this->setAddressService($mainServiceLocator->get('AddressService'));
        }

        if ($this instanceof NationalRegisterAwareInterface) {
            $this->setNationalRegisterConfig($mainServiceLocator->get('Config')['nr']);
        }

        if ($this instanceof OpenAmUserAwareInterface) {
            $this->setOpenAmUser($mainServiceLocator->get(UserInterface::class));
        }

        $this->repoManager = $mainServiceLocator->get('RepositoryServiceManager');

        $this->extraRepos[] = $this->repoServiceName;

        $this->queryHandler = $serviceLocator;

        return $this;
    }

    /**
     * @return RepositoryInterface
     */
    protected function getRepo($name = null)
    {
        if ($name === null) {
            $name = $this->repoServiceName;
        }

        if (!in_array($name, $this->extraRepos)) {
            throw new RuntimeException('You have not injected the ' . $name . ' repository');
        }

        // Lazy load repository
        if (!isset($this->repos[$name])) {
            $this->repos[$name] = $this->repoManager->get($name);
        }

        return $this->repos[$name];
    }

    /**
     * @return \Dvsa\Olcs\Api\Domain\QueryHandlerManager
     */
    protected function getQueryHandler()
    {
        return $this->queryHandler;
    }

    protected function resultList($objects, array $bundle = [])
    {
        return (new ResultList($objects, $bundle))->serialize();
    }

    protected function result($object, array $bundle = [], $values = [])
    {
        return new Result($object, $bundle, $values);
    }
}
