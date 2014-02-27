<?php
namespace Olcs\Db\Controller;

use Zend\View\Model\JsonModel;
use Olcs\Db\Utility\RestServerInterface as OlcsRestServerInterface;

abstract class AbstractBasicRestServerController extends AbstractController implements OlcsRestServerInterface
{
    public function create($data)
    {
        $result = $this->getService()->create($data);

        return new JsonModel(array('id' => $result, 'inputData' => $data));
    }

    public function getList()
    {
        $result = $this->getService()->getList($this->getParams());

        return new JsonModel(array('data' => $result));
    }

    public function get($id)
    {
        $result = $this->getService()->get($id);

        return new JsonModel(array('data' => $result));
    }

    public function update($id, $data)
    {
        $result = $this->getService()->update($data);

        return new JsonModel(array('result' => $result));
    }

    public function patch($id, $data)
    {
        $result = $this->getService()->patch($data);

        return new JsonModel(array('result' => $result));
    }

    public function delete($id)
    {
        $result = $this->getService()->delete($data);

        return new JsonModel(array('result' => $result));
    }

    public function getService()
    {
        return $this->getServiceLocator()->get($this->getControllerName());
    }
}
