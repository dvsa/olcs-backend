<?php

/**
 * SubmissionRecommendation Service
 *  - Takes care of the CRUD actions SubmissionRecommendation entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * SubmissionRecommendation Service
 *  - Takes care of the CRUD actions SubmissionRecommendation entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SubmissionRecommendation extends ServiceAbstract
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
