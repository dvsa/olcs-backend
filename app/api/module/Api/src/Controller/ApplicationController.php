<?php

/**
 * Application Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Http\Response;
use Zend\View\Model\JsonModel;

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

        $applicationService = $this->getServiceLocator()->get('DomainServiceManager')->get('Application');

        $result = $applicationService->handleQuery($dto);

        $response = $this->getResponse();

        if ($result === null) {
            $response->setStatusCode(Response::STATUS_CODE_404);
            $response->setContent(json_encode(['foo' => 'bar']));
            return $response;
        }

        $response->setStatusCode(Response::STATUS_CODE_200);

        return new JsonModel($result);
    }
}
