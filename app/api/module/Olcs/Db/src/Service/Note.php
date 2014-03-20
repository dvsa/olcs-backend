<?php

/**
 * Note Service
 *  - Takes care of the CRUD actions Note entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * Note Service
 *  - Takes care of the CRUD actions Note entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Note extends ServiceAbstract
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
