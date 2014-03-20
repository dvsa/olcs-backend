<?php

/**
 * OrganisationOwner Service
 *  - Takes care of the CRUD actions OrganisationOwner entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * OrganisationOwner Service
 *  - Takes care of the CRUD actions OrganisationOwner entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationOwner extends ServiceAbstract
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
