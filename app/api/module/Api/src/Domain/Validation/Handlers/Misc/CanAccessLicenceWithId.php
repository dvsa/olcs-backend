<?php

/**
 * Can Access Licence With Id
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;

/**
 * Can Access Licence With Id
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CanAccessLicenceWithId extends AbstractHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    /**
     * @inheritdoc
     */
    public function isValid($dto)
    {
        return $this->canAccessLicence($this->getId($dto));
    }

    protected function getId($dto)
    {
        return $dto->getId();
    }
}
