<?php

/**
 * Application Service
 *  - Takes care of the CRUD actions Application entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * Application Service
 *  - Takes care of the CRUD actions Application entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Application extends ServiceAbstract
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
