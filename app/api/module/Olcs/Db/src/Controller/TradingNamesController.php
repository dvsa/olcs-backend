<?php

/**
 * Applications list REST controller
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */

namespace Olcs\Db\Controller;

use Zend\Http\Response;
use Olcs\Db\Exceptions\RestResponseException;

/**
 * Applications list REST controller
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class OrganisationApplicationController extends AbstractBasicRestServerController
{
    protected $allowedMethods = array(
        'getList'
    );

    /**
     * Get list
     *
     * @return Response
     * @throws \Olcs\Db\Exceptions\RestResponseException
     */
    public function getList()
    {
        $options = $this->getDataFromQuery();

        try {
            if (empty($options['organisation'])) {
                throw new RestResponseException('Invalid call', Response::STATUS_CODE_500);
            }
            $response = $this->getService('Organisation')->getApplicationsList($options);

        } catch (\Exception $ex) {

            throw new RestResponseException($ex->getMessage(), Response::STATUS_CODE_500);
        }

        return $this->respond(Response::STATUS_CODE_200, '', $response);
    }
}
