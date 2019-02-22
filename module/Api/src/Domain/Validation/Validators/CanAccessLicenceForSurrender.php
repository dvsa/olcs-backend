<?php


namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\LicenceStatusAwareTrait;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\HandlerInterface;
use Dvsa\Olcs\Api\Entity\Surrender;

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

        $surrender = $this->getRepo('Surrender')->fetchByLicenceId($entityId);


        if ($this->isExternalUser()) {
            return $this->notBeenSurrendered($licence) || $this->hasBeenSigned($surrender) ? parent::isValid($entityId):false;
        }
        return parent::isValid($entityId);
    }
}
