<?php

/**
 * Stay Service
 *  - Takes care of the CRUD actions Stay entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * Stay Service
 *  - Takes care of the CRUD actions Stay entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Stay extends ServiceAbstract
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
