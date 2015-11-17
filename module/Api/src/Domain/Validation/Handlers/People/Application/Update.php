<?php

/**
 * Update
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\People\Application;

/**
 * Update
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Update extends Modify
{
    protected function getPeople($dto)
    {
        return [$this->getRepo('Person')->fetchById($dto->getPerson())];
    }
}
