<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\HandlerInterface;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Class CanConfirmSurrender
 *
 * @package Dvsa\Olcs\Api\Domain\Validation\Validators
 */
class CanConfirmSurrender extends AbstractCanAccessEntity implements HandlerInterface
{
    use SurrenderStatusAwareTrait;

    protected $repo = 'Surrender';

    public function isValid($dto)
    {
        $entityId = $dto->getId();
        $surrender = $this->getRepo($this->repo)->fetchOneByLicenceId($entityId);

        if ($this->hasBeenSigned($surrender)) {
            return parent::isValid($surrender->getId());
        }

        return false;
    }
}
