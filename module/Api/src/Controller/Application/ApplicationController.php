<?php

/**
 * Application Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Controller\Application;

use Zend\Mvc\Controller\AbstractRestfulController;
use Dvsa\Olcs\Api\Domain\Exception\Exception;

/**
 * Application Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationController extends AbstractRestfulController
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

    public function create($data)
    {
        $dto = $this->params('dto');

        try {
            $result = $this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($dto);
            return $this->response()->successfulCreate($result);
        } catch (Exception $ex) {
            return $this->response()->error(400, $ex->getMessages());
        } catch (\Exception $ex) {
            return $this->response()->error(500, [$ex->getMessage()]);
        }
    }
}
