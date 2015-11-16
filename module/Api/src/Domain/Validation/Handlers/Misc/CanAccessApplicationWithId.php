<?php

/**
 * Can Access Application With Id
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;

/**
 * Can Access Application With Id
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CanAccessApplicationWithId extends AbstractHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    /**
     * @inheritdoc
     */
    public function isValid($dto)
    {
        return $this->canAccessApplication($this->getId($dto));
    }

    protected function getId($dto)
    {
        return $dto->getId();
    }
}
