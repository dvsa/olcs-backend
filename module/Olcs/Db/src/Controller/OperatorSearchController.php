<?php

/**
 * Operator Search REST controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Controller;

use Zend\Http\Response;

/**
 * Operator Search REST controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatorSearchController extends AbstractBasicRestServerController
{
    protected $allowedMethods = array(
        'getList'
    );

    public function getList()
    {
        $options = $this->getDataFromQuery();

        try {
            $data = $this->getService('Licence')->findLicences($options);

        } catch(\Exception $ex) {

            throw new RestResponseException($ex->getMessage(), Response::STATUS_CODE_500);
        }

        return $this->respond(Response::STATUS_CODE_200, '', $data);
    }
}
