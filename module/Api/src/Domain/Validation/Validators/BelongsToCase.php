<?php

/**
 * Belongs to Case
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Entity\CaseProviderInterface;

/**
 * Belongs to Case
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class BelongsToCase extends AbstractValidator
{
    public function isValid(CaseProviderInterface $entity, $caseId)
    {
        return $entity->getCase()->getId() == $caseId;
    }
}
