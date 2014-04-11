<?php

/**
 * Statement Service
 *  - Takes care of the CRUD actions Statement entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * Statement Service
 *  - Takes care of the CRUD actions Statement entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Statement extends ServiceAbstract
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
