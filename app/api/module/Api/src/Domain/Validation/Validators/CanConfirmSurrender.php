<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\HandlerInterface;

/**
 * Class CanConfirmSurrender
 *
 * @package Dvsa\Olcs\Api\Domain\Validation\Validators
 */
class CanConfirmSurrender extends AbstractCanAccessEntity implements HandlerInterface
{
    use SurrenderStatusAwareTrait;

    protected $repo = 'Licence';

    public function isValid($dto)
    {
        $entityId = $dto->getId();
        $surrender = $this->getRepo('Surrender')->fetchOneByLicenceId($entityId);

        if ($this->hasBeenSigned($surrender)) {
            return parent::isValid($entityId);
        }

        return false;
    }
}
