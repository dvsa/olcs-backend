<?php declare(strict_types=1);

namespace Dvsa\Olcs\DocumentShare\Service;


use Dvsa\Olcs\Api\Entity\User\User;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class DocumentClientFactory
 *
 * @package Dvsa\Olcs\DocumentShare\Service
 */
class DocumentClientStrategy
{

    /**
     * @var string
     */
    protected $clientClass;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DocumentStoreInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $authService = $serviceLocator->get(AuthorizationService::class);

        /** @var User $currentUser */
        $currentUser = $authService->getIdentity()->getUser();

        $this->clientClass = ($currentUser->getOsType() === User::USER_OS_TYPE_WINDOWS_10) ? WebDavClient::class : DocManClient::class;

        return $this;
    }

    /**
     *
     *
     * @return string
     */
    public function getClientClass(): string
    {
        return $this->client;
    }
}
