<?php

namespace Dvsa\Olcs\Api\Service\Qa\Supports;

use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;

trait AnyTrait
{
    /**
     * Whether the passed entity is any entity (always returns true)
     *
     * @param QaEntityInterface $qaEntity
     *
     * @return bool
     */
    public function supports(QaEntityInterface $qaEntity)
    {
        return true;
    }
}
