<?php

/**
 * Not Found Exception
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Exception;

/**
 * Not Found Exception
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class NotFoundException extends Exception
{
    public function __construct($message)
    {
        $this->messages = [$message];
    }
}
