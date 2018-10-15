<?php


namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\GdsVerify\ProcessSignatureResponse;
use Dvsa\Olcs\Transfer\Query\GdsVerify\GetAuthRequest;

class CanVerify extends AbstractHandler implements AuthAwareInterface
{
    use AuthAwareTrait;


    /**
     * @param ProcessSignatureResponse $dto
     *
     * @return bool
     */
    public function isValid($dto)
    {
        $allowed = $this->isOperator();

        $allowed = $allowed || $this->isValidAccess($dto);

        return $allowed;
    }

    /**
     * isTransportManager
     *
     * @return bool
     */
    private function isTransportManager()
    {
        return $this->isGranted(Permission::TRANSPORT_MANAGER);
    }

    private function isValidAccess($dto)
    {
        if ($this->isTransportManager() && $dto instanceof ProcessSignatureResponse) {
            if (method_exists($dto, 'getTransportManagerApplication')) {
                return $dto->getTransportManagerApplication() > 0;
            }

        }
        return false;
    }
}
