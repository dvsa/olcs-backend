<?php

/**
 * Validation Exception
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Exception;

/**
 * Validation Exception
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ValidationException extends Exception
{
    public function __construct(array $messages)
    {
        $this->messages = $messages;
    }
}
