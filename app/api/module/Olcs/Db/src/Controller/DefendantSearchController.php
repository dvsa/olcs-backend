<?php

/**
 * Defendant Search REST controller
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
namespace Olcs\Db\Controller;

use Zend\Http\Response;
use Olcs\Db\Exceptions\RestResponseException;

/**
 * Defendant Search REST controller
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class DefendantSearchController extends AbstractBasicRestServerController
{
    protected $allowedMethods = array('get', 'getList');

    /**
     * Get List Service method
     *
     * @return array list results
     * @throws RestResponseException
     */
    public function getList()
    {
        $options = $this->getDataFromQuery();

        try {

            $data = $this->getService('Person')->findPersons($options);

        } catch (\Exception $ex) {

            throw new RestResponseException($ex->getMessage(), Response::STATUS_CODE_500);
        }

        $response = array(
            'Type' => 'results',
            'Results' => $data
        );

        return $this->respond(Response::STATUS_CODE_200, '', $response);
    }

}
