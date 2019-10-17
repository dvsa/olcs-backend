<?php declare(strict_types=1);

namespace Dvsa\Olcs\DocumentShare\Service;


use Dvsa\Olcs\Api\Entity\User\User;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

class DocumentClientFactory
{
    private $serviceLocator;

    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getDocumentClient() : DocumentStoreInterface
    {
        $authService = $this->serviceLocator->get(AuthorizationService::class);

        /** @var User $currentUser */
        $currentUser = $authService->getIdentity()->getUser();

        // do windows 10 check here
        if ($currentUser->getOsType() === User::USER_OS_TYPE_WINDOWS_10) {
            return $this->serviceLocator->get(WebDavClient::class);
        } else {
            return $this->serviceLocator->get(DocManClient::class);
        }
    }
}
