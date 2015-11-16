<?php

/**
 * Does Own Licence With Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc;

/**
 * Does Own Licence With Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DoesOwnLicenceWithLicence extends DoesOwnLicenceWithId
{
    protected function getId($dto)
    {
        return $dto->getLicence();
    }
}
