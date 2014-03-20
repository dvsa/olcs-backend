<?php

/**
 * ContactDetails Service
 *  - Takes care of the CRUD actions ContactDetails entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * ContactDetails Service
 *  - Takes care of the CRUD actions ContactDetails entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ContactDetails extends ServiceAbstract
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
