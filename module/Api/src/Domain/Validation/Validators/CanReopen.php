<?php

/**
 * Can Reopen
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Entity\ReopenableInterface;

/**
 * Can Reopen
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class CanReopen extends AbstractValidator
{
    public function isValid(ReopenableInterface $entity)
    {
        return $entity->canReopen();
    }
}
