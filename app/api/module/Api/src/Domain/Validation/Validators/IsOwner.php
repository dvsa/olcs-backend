<?php

/**
 * Is Owner
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;

/**
 * Is Owner
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class IsOwner extends AbstractValidator implements AuthAwareInterface
{
    use AuthAwareTrait;

    public function isValid(OrganisationProviderInterface $entity)
    {
        // This is needed as if user has no organisation and entity has no organisation they would be granted access
        if ($this->getCurrentUser()->getRelatedOrganisation() === null) {
            return false;
        }

        return $this->getCurrentUser()->getRelatedOrganisation() === $entity->getRelatedOrganisation();
    }
}
