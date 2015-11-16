<?php

/**
 * Does Own Application With Id
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;

/**
 * Does Own Application With Id
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DoesOwnApplicationWithId extends AbstractHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    /**
     * @inheritdoc
     */
    public function isValid($dto)
    {
        if ($this->isInternalUser()) {
            return true;
        }

        return $this->doesOwnApplication($this->getId($dto));
    }

    protected function getId($dto)
    {
        return $dto->getId();
    }
}
