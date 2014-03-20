<?php

/**
 * Fee Service
 *  - Takes care of the CRUD actions Fee entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * Fee Service
 *  - Takes care of the CRUD actions Fee entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Fee extends ServiceAbstract
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
