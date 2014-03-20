<?php

/**
 * CaseNote Service
 *  - Takes care of the CRUD actions CaseNote entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * CaseNote Service
 *  - Takes care of the CRUD actions CaseNote entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CaseNote extends ServiceAbstract
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
