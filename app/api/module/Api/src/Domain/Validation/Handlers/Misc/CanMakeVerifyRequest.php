<?php


namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;
use Dvsa\Olcs\Api\Entity\User\Permission;

class CanMakeVerifyRequest extends AbstractHandler implements AuthAwareInterface
{
    use AuthAwareTrait;


    /**
     * @inheritdoc
     */
    public function isValid($dto)
    {
        return ($this->isOperator()|| $this->isGranted(Permission::TRANSPORT_MANAGER));
    }
}
