<?php

/**
 * Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Controller\Licence;

use Zend\Mvc\Controller\AbstractRestfulController;
use Dvsa\Olcs\Api\Domain\Exception\Exception;

/**
 * Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceController extends AbstractRestfulController
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
