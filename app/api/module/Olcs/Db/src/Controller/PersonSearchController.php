<?php

/**
 * Person Search REST controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Controller;

use Zend\Http\Response;
use Olcs\Db\Exceptions\RestResponseException;

/**
 * Person Search REST controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PersonSearchController extends AbstractBasicRestServerController
{
    protected $allowedMethods = array('getList');

    public function getList()
    {
        $options = $this->getDataFromQuery();

        try {

            $data = $this->getService('Licence')->findAllPersons($options);

        } catch(\Exception $ex) {

            throw new RestResponseException($ex->getMessage(), Response::STATUS_CODE_500);
        }

        $response = array(
            'Type' => 'results',
            'Results' => $data
        );

        return $this->respond(Response::STATUS_CODE_200, '', $response);
    }
}
