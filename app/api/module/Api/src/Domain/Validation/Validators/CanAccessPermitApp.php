<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

/**
 * Can access permit app
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class CanAccessPermitApp extends AbstractCanAccessEntity
{
    protected $repo = 'EcmtPermitApplication';
}
