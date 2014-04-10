<?php

/**
 * Licence REST controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Controller;

use Zend\Http\Response;
use Olcs\Db\Exceptions\RestResponseException;
use Olcs\Db\Traits\RestResponseTrait;
use Olcs\Db\Exceptions\NoVersionException;
use Doctrine\ORM\OptimisticLockException;

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
    
    /**
     * Update and patch give the same response so no need to duplicate
     *
     * @param id $id
     * @param mixed $data
     * @param string $method
     * @return Response
     */
    protected function updateOrPatch($id, $data, $method)
    {
        $data = $this->formatDataFromJson($data);
        if ($data instanceof Response) {
            return $data;
        }
        
        try {
            if ($this->getService('Organisation')->updateByLicenceId($id, $data)) {
                return $this->respond(Response::STATUS_CODE_200, 'Entity updated');
            }

            return $this->respond(Response::STATUS_CODE_404, 'Entity not found');

        } catch (NoVersionException $ex) {

            return $this->respond(Response::STATUS_CODE_400, 'No version number sent');

        } catch (OptimisticLockException $ex) {

            $result = $this->getService('Organisation')->getByLicenceId($id);

            return $this->respond(Response::STATUS_CODE_409, 'This entity has been updated since', $result);

        } catch (\Exception $ex) {

            return $this->unknownError($ex);
        }
    }
    
}
