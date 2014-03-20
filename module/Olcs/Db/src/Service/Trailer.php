<?php

/**
 * Trailer Service
 *  - Takes care of the CRUD actions Trailer entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * Trailer Service
 *  - Takes care of the CRUD actions Trailer entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Trailer extends ServiceAbstract
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
