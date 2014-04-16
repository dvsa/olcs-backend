<?php

/**
 * Operating Centre REST controller
 *
 * @author Jess Rowbottom <jess.rowbottom@valtech.co.uk>
 */

namespace Olcs\Db\Controller;

use Zend\Http\Response;
use Olcs\Db\Exceptions\RestResponseException;
use Olcs\Db\Traits\RestResponseTrait;
use Olcs\Db\Exceptions\NoVersionException;
use Doctrine\ORM\OptimisticLockException;

/**
 * Licence Operating Centre REST controller
 *
 * @author Jess Rowbottom <jess.rowbottom@valtech.co.uk>
 */
class LicenceOperatingCentreController extends AbstractBasicRestServerController
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
            $result = $this->getService('OperatingCentre')->getByLicenceId($id);
            if (empty($result)) {

                return $this->respond(Response::STATUS_CODE_404, 'Entity not found');
            }

            return $this->respond(Response::STATUS_CODE_200, 'Entity found', $result);

        } catch (\Exception $ex) {
            return $this->unknownError($ex);
        }
    }
    
}
