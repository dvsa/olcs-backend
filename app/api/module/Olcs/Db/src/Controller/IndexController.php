<?php
namespace Olcs\Db\Controller;

use Zend\View\Model\JsonModel;

class IndexController extends AbstractController
{
    public function getList()
    {
        //throw new \Exception();

        return new JsonModel(array('TestKey'=>'Test value'));
    }
}
