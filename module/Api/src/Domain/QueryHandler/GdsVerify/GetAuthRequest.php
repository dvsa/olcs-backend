<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\GdsVerify;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->setGdsVerifyService($container->get(\Dvsa\Olcs\GdsVerify\Service\GdsVerify::class));
        return parent::__invoke($container, $requestedName, $options);
    }

    /**
     * Handle query
     *
     * @param QueryInterface $query Query to handle
     *
     * @return array
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
}
