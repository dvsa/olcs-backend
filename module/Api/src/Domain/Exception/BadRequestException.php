<?php

/**
 * BadRequestException
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Exception;

/**
 * BadRequestException
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BadRequestException extends Exception
{
    public function __construct($message)
    {
        $this->messages = [$message];
    }
}
