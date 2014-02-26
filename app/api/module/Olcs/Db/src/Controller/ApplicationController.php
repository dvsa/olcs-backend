<?php
namespace Olcs\Db\Controller;

use Zend\View\Model\JsonModel;

class ApplicationController extends AbstractController
{
    public function getList()
    {
        //throw new \Exception();

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

    public function delete($id)
    {
        return new JsonModel(array('Application'=>'delete'));
    }
}
