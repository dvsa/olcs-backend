<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

/**
 * Can edit an IRHP permit application
 */
class CanEditIrhpPermitApplicationWithId extends AbstractCanEditEntity
{
    protected $repo = 'IrhpPermitApplication';
}
