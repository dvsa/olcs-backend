<?php

/**
 * Person Service
 *  - Takes care of the CRUD actions Person entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * Person Service
 *  - Takes care of the CRUD actions Person entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Person extends ServiceAbstract
{

    /**
     * Returns an indexed array of valid search terms for this service / entity.
     *
     * @return array
     */
    public function getValidSearchFields()
    {
        return array(
            'firstName',
            'surname'
        );
    }

}
