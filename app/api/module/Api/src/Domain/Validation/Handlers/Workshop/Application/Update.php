<?php

/**
 * Update
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\Workshop\Application;

/**
 * Update
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class Update extends Modify
{
    protected function getWorkshops($dto)
    {
        return [$this->getRepo('Workshop')->fetchById($dto->getId())];
    }
}
