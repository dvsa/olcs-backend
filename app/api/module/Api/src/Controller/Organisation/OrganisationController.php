<?php

/**
 * Organisation Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Controller\Organisation;

use Zend\Mvc\Controller\AbstractRestfulController;

/**
 * Organisation Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationController extends AbstractRestfulController
{
    public function get($id)
    {
        try {
            $result = $this->getServiceLocator()->get('QueryHandlerManager')->handleQuery($this->params('dto'));
            return $this->response()->singleResult($result);
        } catch (\Exception $ex) {
            return $this->response()->notFound();
        }
    }
}
