<?php

/**
 * LicenceVehicle REST controller
 *
 * @author S Lizzio <shaun.lizzio@valtech.co.uk>
 */

namespace Olcs\Db\Controller;
use Zend\Http\Response;
use Olcs\Db\Exceptions\RestResponseException;
use Olcs\Db\Traits\RestResponseTrait;
use Olcs\Db\Exceptions\NoVersionException;
use Doctrine\ORM\OptimisticLockException;
/**
 * LicenceVehicle REST controller
 *
 * @author S Lizzio <shaun.lizzio@valtech.co.uk>
 */
class LicenceVehicleController extends AbstractBasicRestServerController
{

    /**
     * @todo dependent on multi entity work being completed. For now we query
     * the vehicle entities to produce a list.
     * 
     * Get a list of vehicles
     *
     * @return Response
     */
    public function getList()
    {
        $this->checkMethod(__METHOD__);

        $data = $this->getDataFromQuery();
        try {
            // waiting for mechanism to extract additional table data 
            $result = $this->getService('LicenceVehicle')->getVehicleList($data);
            
            if (empty($result)) {

                return $this->respond(Response::STATUS_CODE_200, 'No results found');
            }

            return $this->respond(Response::STATUS_CODE_200, 'Results found', $result);

        } catch (\Exception $ex) {

            throw new RestResponseException($ex->getMessage(), Response::STATUS_CODE_500);
        }
    }
}
