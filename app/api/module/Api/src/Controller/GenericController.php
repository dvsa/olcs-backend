<?php

namespace Dvsa\Olcs\Api\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

/**
 * Generic Controller
 */
class GenericController extends AbstractRestfulController
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

    public function getList()
    {
        try {
            $result = $this->getServiceLocator()->get('QueryHandlerManager')->handleQuery($this->params('dto'));
            return $this->response()->multipleResults($result['count'], $result['result']);
        } catch (\Exception $ex) {
            var_dump($ex->getMessage());
            return $this->response()->notFound();
        }
    }
}
