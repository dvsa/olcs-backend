<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

/**
 * Can edit permit app
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class CanEditPermitApp extends AbstractCanEditEntity
{
    protected $repo = 'EcmtPermitApplication';
}
