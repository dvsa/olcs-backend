<?php
namespace Olcs\Db\Controller;

use Zend\View\Model\JsonModel;
use Olcs\Db\Utility\RestServerInterface as OlcsRestServerInterface;

class ApplicationController extends AbstractController implements OlcsRestServerInterface
{
    public function getList()
    {
        return new JsonModel(array('Application'=>'getList'));
    }

    public function get($id)
    {
        return new JsonModel(array('Application'=>'get'));
    }

    public function create($data)
    {
        return new JsonModel(array('Application'=>'create'));
    }

    public function update($id, $data)
    {
        return new JsonModel(array('Application'=>'update'));
    }

    public function patch($id, $data)
    {
        //
    }

    public function delete($id)
    {
        return new JsonModel(array('Application'=>'delete'));
    }
}
