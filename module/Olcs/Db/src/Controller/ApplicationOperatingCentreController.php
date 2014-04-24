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
class ApplicationOperatingCentreController extends AbstractBasicRestServerController
{

    /**
     * Get operating centres in an application by id
     *
     * @param int $id
     * @return Response
     */
    public function getList()
    {
        $this->checkMethod(__METHOD__);
        $options = $this->getDataFromQuery();

        try {
            $result = $this->getService('ApplicationOperatingCentre')->getByApplicationId($options);
            return $this->respond(Response::STATUS_CODE_200, 'Entity found', $result);

        } catch (\Exception $ex) {
            return $this->unknownError($ex);
        }
    }

}
