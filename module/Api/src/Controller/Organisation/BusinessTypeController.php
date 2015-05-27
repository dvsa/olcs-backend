<?php

/**
 * Business Type Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Controller\Organisation;

use Zend\Mvc\Controller\AbstractRestfulController;
use Dvsa\Olcs\Api\Domain\Exception\Exception;

/**
 * Business Type Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessTypeController extends AbstractRestfulController
{
    public function update($id, $data)
    {
        $dto = $this->params('dto');

        try {
            $result = $this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($dto);
            return $this->response()->successfulUpdate($result);
        } catch (Exception $ex) {
            return $this->response()->error(400, $ex->getMessages());
        } catch (\Exception $ex) {
            return $this->response()->error(500, [$ex->getMessage(), explode('#', $ex->getTraceAsString())]);
        }
    }
}
