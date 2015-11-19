<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

/**
 * Can Access TransportManagerApplication
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CanAccessTransportManagerApplication extends AbstractCanAccessEntity
{
    protected $repo = 'TransportManagerApplication';
}
