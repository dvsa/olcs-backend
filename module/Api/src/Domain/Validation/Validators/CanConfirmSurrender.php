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
    protected $repo = 'Surrender';

    /**
     * @var Surrender
     */
    private $surrender;

    public function isValid($dto)
    {
        $entityId = $dto->getId();
        $surrender = $this->getRepo($this->repo)->fetchOneByLicenceId($entityId);

        if ($surrender->getStatus()->getId() === RefData::SURRENDER_STATUS_SIGNED) {
            return parent::isValid($entityId);
        }

        return false;
    }
}
