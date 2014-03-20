<?php

/**
 * Address Service
 *  - Takes care of the CRUD actions Address entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * Address Service
 *  - Takes care of the CRUD actions Address entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Address extends ServiceAbstract
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
