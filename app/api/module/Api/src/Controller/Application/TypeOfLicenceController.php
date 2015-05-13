<?php

/**
 * Type Of Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Controller\Application;

use Zend\Mvc\Controller\AbstractRestfulController;

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

        $result = $this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($dto);

        print '<pre>';
        print_r($result);
        exit;
    }
}
