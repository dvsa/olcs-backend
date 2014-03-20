<?php

/**
 * Vehicle Service
 *  - Takes care of the CRUD actions Vehicle entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * Vehicle Service
 *  - Takes care of the CRUD actions Vehicle entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Vehicle extends ServiceAbstract
{

    /**
     * Returns an indexed array of valid search terms for this service / entity.
     *
     * @return array
     */
    public function getValidSearchFields()
    {
        return array();
    }

}
