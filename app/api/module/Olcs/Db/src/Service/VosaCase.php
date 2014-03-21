<?php

/**
 * VosaCase Service
 *  - Takes care of the CRUD actions VosaCase entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * VosaCase Service
 *  - Takes care of the CRUD actions VosaCase entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VosaCase extends ServiceAbstract
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
