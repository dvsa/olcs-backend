<?php

/**
 * RestResponseException
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Exception;

/**
 * RestResponseException
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class RestResponseException extends Exception
{
    public function __construct($message = null, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->messages = [$message];
    }
}
