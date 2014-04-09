<?php

/**
 * Licence REST controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Controller;

use Zend\Http\Response;

/**
 * Licence REST controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceOrganisationController extends AbstractBasicRestServerController
{

    /**
     * Get an entity by it's id
     *
     * @param int $id
     * @return Response
     */
    public function get($id)
    {
        $this->checkMethod(__METHOD__);
    
        try {
            $result = $this->getService('Organisation')->getByLicenceId($id);
            if (empty($result)) {
    
                return $this->respond(Response::STATUS_CODE_404, 'Entity not found');
            }
    
            return $this->respond(Response::STATUS_CODE_200, 'Entity found', $result);
    
        } catch (\Exception $ex) {
            return $this->unknownError($ex);
        }
    }
    
}
