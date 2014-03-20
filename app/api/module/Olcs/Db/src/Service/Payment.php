<?php

/**
 * Payment Service
 *  - Takes care of the CRUD actions Payment entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * Payment Service
 *  - Takes care of the CRUD actions Payment entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Payment extends ServiceAbstract
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
