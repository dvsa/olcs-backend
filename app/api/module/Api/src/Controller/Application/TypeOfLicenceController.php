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

        $applicationService = $this->getServiceLocator()->get('DomainServiceManager')->get('Application');

        $result = $applicationService->handleCommand($dto);

        print '<pre>';
        print_r($result);
        exit;
    }
}
