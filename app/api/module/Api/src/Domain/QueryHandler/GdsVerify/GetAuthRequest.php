<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\GdsVerify;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * GetAuthRequest
 */
class GetAuthRequest extends AbstractQueryHandler
{
    protected $repoServiceName = 'SystemParameter';

    /** @var  \Dvsa\Olcs\GdsVerify\Service\GdsVerify */
    private $gdsVerifyService;

    /**
     * Factory
     *
     * @param ServiceLocatorInterface $serviceLocator Service locator
     *
     * @return $this|\Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler|mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this->__invoke($serviceLocator, GetAuthRequest::class);
    }

    /**
     * Handle query
     *
     * @param QueryInterface $query Query to handle
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handleQuery(QueryInterface $query)
    {
        $disabled = $this->getRepo()->getDisableGdsVerifySignatures();
        if (!$disabled) {
            $data = $this->getGdsVerifyService()->getAuthenticationRequest();
        }
        $data['enabled'] = !$disabled;

        return $data;
    }

    /**
     * Get the GDS Verify service
     *
     * @return \Dvsa\Olcs\GdsVerify\Service\GdsVerify
     */
    public function getGdsVerifyService()
    {
        return $this->gdsVerifyService;
    }

    /**
     * Set the GDS Verify service
     *
     * @param \Dvsa\Olcs\GdsVerify\Service\GdsVerify $gdsVerifyService Verify service
     *
     * @return void
     */
    public function setGdsVerifyService(\Dvsa\Olcs\GdsVerify\Service\GdsVerify $gdsVerifyService)
    {
        $this->gdsVerifyService = $gdsVerifyService;
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return GetAuthRequest
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $fullContainer = $container;

        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }

        $this->setGdsVerifyService($container->get(\Dvsa\Olcs\GdsVerify\Service\GdsVerify::class));
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}
