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
        return $this->getCurrentUser()->getRelatedOrganisation() === $entity->getRelatedOrganisation();
    }
}
