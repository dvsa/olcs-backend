<?php

/**
 * Checklists
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Olcs\Db\Controller\ContinuationDetail;

use Olcs\Db\Controller\AbstractController;
use Zend\Http\Response;

/**
 * Checklists
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ChecklistsController extends AbstractController
{
    public function create($data)
    {
        $data = $this->formatDataFromJson($data);

        if ($data instanceof Response) {
            return $data;
        }

        $response = $this->getServiceLocator()->get('ContinuationDetail/Checklists')
            ->generate($data['ids']);
    }
}
