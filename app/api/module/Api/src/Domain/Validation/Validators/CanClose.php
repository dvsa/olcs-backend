<?php

/**
 * Can Close
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Entity\CloseableInterface;

/**
 * Can Close
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class CanClose extends AbstractValidator
{
    public function isValid(CloseableInterface $entity)
    {
        return $entity->canClose();
    }
}
