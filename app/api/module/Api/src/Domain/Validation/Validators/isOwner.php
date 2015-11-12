<?php

/**
 * Is Owner
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;
use Dvsa\Olcs\Api\Entity\User\User;
use Zend\ServiceManager\FactoryInterface;

/**
 * Is Owner
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class IsOwner
{
    public function isValid(OrganisationProviderInterface $entity, User $user)
    {
        return $user->getRelatedOrganisation() === $entity->getRelatedOrganisation();
    }
}
