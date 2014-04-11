<?php

/**
 * Submission Service
 *  - Takes care of the CRUD actions Submission entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * Submission Service
 *  - Takes care of the CRUD actions Submission entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Submission extends ServiceAbstract
{

    /**
     * Returns an indexed array of valid search terms for this service / entity.
     *
     * @return array
     */
    public function getValidSearchFields()
    {
        return array('vosaCase');
    }

}
