<?php

/**
 * Runtime Exception
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Exception;

/**
 * Runtime Exception
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RuntimeException extends Exception
{
    public function __construct($message)
    {
        $this->messages = [$message];

        parent::__construct($message);
    }
}
