<?php

/**
 * Does Own Application With Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc;

/**
 * Does Own Application With Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DoesOwnApplicationWithApplication extends DoesOwnApplicationWithId
{
    protected function getId($dto)
    {
        return $dto->getApplication();
    }
}
