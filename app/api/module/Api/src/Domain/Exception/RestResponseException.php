<?php

/**
 * BadRequestException
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Exception;

/**
 * BadRequestException
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class RestResponseException extends Exception
{
    public function __construct($message = null)
    {
        $this->messages = [$message];
    }
}
