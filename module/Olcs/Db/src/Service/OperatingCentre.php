<?php

/**
 * OperatingCentre Service
 *  - Takes care of the CRUD actions OperatingCentre entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * OperatingCentre Service
 *  - Takes care of the CRUD actions OperatingCentre entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentre extends ServiceAbstract
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
