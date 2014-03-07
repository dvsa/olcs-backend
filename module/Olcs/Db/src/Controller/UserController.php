<?php
namespace Olcs\Db\Controller;

use Zend\View\Model\JsonModel;
use Olcs\Db\Utility\RestServerInterface as OlcsRestServerInterface;

class UserController extends AbstractBasicRestServerController
{
    public function getRolesAction()
    {
        $id = (int) $this->plugin('params')->fromRoute('id', false);
        //die(var_export($id, 1));

        if (!$id) {
            return new JsonModel(array('result' => 'fail', 'message' => 'ID not found'));
        }

        $extractedRoles = $this->getService()->getRoles($id);

        return new JsonModel(array('result' => 'ok', 'data' => $extractedRoles));
    }
}
