<?php

/**
 * Update
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\OrganisationPerson;

/**
 * Update
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Update extends Modify
{
    protected function getIds($dto)
    {
        return [$dto->getId()];
    }
}
