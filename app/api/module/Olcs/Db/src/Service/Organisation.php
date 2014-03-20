<?php

/**
 * Organisation Service
 *  - Takes care of the CRUD actions Organisation entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * Organisation Service
 *  - Takes care of the CRUD actions Organisation entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Organisation extends ServiceAbstract
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
