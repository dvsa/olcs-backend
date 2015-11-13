<?php

/**
 * Role List
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\User;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;

/**
 * Role List
 */
class RoleList extends AbstractHandler
{
    /**
     * @inheritdoc
     */
    public function isValid($dto)
    {
        // required by the role provider so available to everyone
        return true;
    }
}
