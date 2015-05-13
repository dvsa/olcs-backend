<?php

/**
 * Application Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Controller\Application;

use Zend\Mvc\Controller\AbstractRestfulController;

/**
 * Application Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationController extends AbstractRestfulController
{
    public function get($id)
    {
        $dto = $this->params('dto');

        $result = $this->getServiceLocator()->get('QueryHandlerManager')->handleQuery($dto);

        if ($result === null) {

            return $this->response()->notFound();
        }

        return $this->response()->singleResult($result);
    }
}
