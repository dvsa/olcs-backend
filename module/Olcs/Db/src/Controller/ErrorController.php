<?php
namespace Olcs\Db\Controller;

use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Olcs\Db\Traits\RestResponseTrait;

class ErrorController extends AbstractActionController
{
    use RestResponseTrait;

    public function indexAction()
    {
        return $this->respond(Response::STATUS_CODE_404, 'Resource not found');
    }
}
