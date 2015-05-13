<?php

/**
 * Type Of Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Controller\Application;

use Zend\Mvc\Controller\AbstractRestfulController;
use Dvsa\Olcs\Api\Domain\Exception\Exception;

/**
 * Type Of Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TypeOfLicenceController extends AbstractRestfulController
{
    public function update($id, $data)
    {
        $dto = $this->params('dto');

        try {
            $messages = $this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($dto);
            return $this->response()->successfulUpdate($messages);
        } catch (Exception $ex) {
            return $this->response()->error(500, $ex->getMessages());
        } catch (\Exception $ex) {
            throw $ex;
            //return $this->response()->error(500, [$ex->getMessage()]);
        }
    }
}
