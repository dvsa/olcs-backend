<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\LicenceStatusAwareTrait;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\HandlerInterface;

class CanAccessLicenceForSurrender extends CanAccessLicence implements HandlerInterface
{
    use LicenceStatusAwareTrait;
    use SurrenderStatusAwareTrait;
    use AuthAwareTrait;

    protected $repo = 'Licence';

    public function isValid($dto)
    {
        $entityId = $dto->getId($dto);

        $licence = $this->getRepo($this->repo)->fetchById($entityId);
        $surrender = $this->getRepo('Surrender')->fetchOneByLicenceId($entityId);

        if ($this->isExternalUser()) {
            return $this->notBeenSurrendered($licence) || $this->hasBeenSigned($surrender)  ? parent::isValid($entityId):false;
        }
        return parent::isValid($entityId);
    }
}
