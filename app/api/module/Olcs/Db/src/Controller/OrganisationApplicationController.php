<?php

/**
 * Operator Search REST controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Controller;

use Zend\Http\Response;
use Olcs\Db\Exceptions\RestResponseException;

/**
 * Operator Search REST controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationApplicationController extends AbstractBasicRestServerController
{
    protected $allowedMethods = array(
        'getList'
    );

    public function getList()
    {
        $options = $this->getDataFromQuery();

        try {
            if (empty($options['operatorId'])){
                throw new RestResponseException('Invalid call', Response::STATUS_CODE_500);
            }
            $response = $this->getService('Organisation')->getApplicationsList($options);

        } catch(\Exception $ex) {

            throw new RestResponseException($ex->getMessage(), Response::STATUS_CODE_500);
        }

        return $this->respond(Response::STATUS_CODE_200, '', $response);
    }
}
