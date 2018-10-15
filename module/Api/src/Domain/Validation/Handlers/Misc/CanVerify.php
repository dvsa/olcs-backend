<?php


namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\GdsVerify\ProcessSignatureResponse;

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
        if (!empty($dto->getTransportManagerApplication())) {
            $allowed = $allowed || $this->isTransportManager();
        }
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
}
