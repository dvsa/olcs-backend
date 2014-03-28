<?php

/**
 * CaseCategory Service
 *  - Takes care of the CRUD actions CaseCategoryLink entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * CaseCategory Service
 *  - Takes care of the CRUD actions CaseCategoryLink entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CaseCategory extends ServiceAbstract
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
