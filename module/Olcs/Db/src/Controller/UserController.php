<?php
namespace Olcs\Db\Controller;

use Zend\View\Model\JsonModel;
use Olcs\Db\Utility\RestServerInterface as OlcsRestServerInterface;

class UserController extends AbstractBasicRestServerController
{


    public function getRolesAction()
    {
        $id = (int) $this->plugin('params')->fromQuery('id', false);

        if (!$id) {
            return new JsonModel(array('result' => 'fail', 'message' => 'ID not found'));
        }
    }
}
