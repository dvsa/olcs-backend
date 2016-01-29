<?php

namespace Dvsa\Olcs\Api\Service;

/**
 * CPMS Identity Provider Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
use CpmsClient\Authenticate;

/**
 * CPMS Identity Provider Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CpmsIdentityProvider implements Authenticate\IdentityProviderInterface
{
    use Authenticate\IdentityProviderTrait;
}
