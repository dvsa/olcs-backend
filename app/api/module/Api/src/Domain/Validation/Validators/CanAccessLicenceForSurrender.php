<?php


namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\LicenceStatusAwareTrait;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\HandlerInterface;

class CanAccessLicenceForSurrender extends AbstractCanAccessEntity implements HandlerInterface
{
    use LicenceStatusAwareTrait;
    use AuthAwareTrait;

    protected $repo = 'Licence';

    public function isValid($dto)
    {
        $entityId = $dto->getId($dto);

        $licence = $this->getRepo($this->repo)->fetchById($entityId);

        if ($this->isExternalUser() && $this->NotBeenSurrendered($licence)) {
            return $this->canAccessLicence($entityId);
        }
        return $this->canAccessLicence($entityId);
    }
}
