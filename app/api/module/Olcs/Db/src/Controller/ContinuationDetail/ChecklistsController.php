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
        $formattedData = $this->formatDataFromJson($data);

        if ($formattedData instanceof Response) {
            return $formattedData;
        }

        $response = $this->getServiceLocator()->get('ContinuationDetail/Checklists')
            ->generate($formattedData['ids']);

        if ($response === true) {
            return $this->respond(Response::STATUS_CODE_201, 'Entity Created', []);
        }

        // @NOTE A failed response is either a string or an exception
        // we may need to decide how to handle errors that are not 500s

        return $this->unknownError($response);
    }

    public function update($id, $data)
    {
        $formattedData = $this->formatDataFromJson($data);

        if ($formattedData instanceof Response) {
            return $formattedData;
        }

        $response = $this->getServiceLocator()->get('ContinuationDetail/Checklists')
            ->update($id, $formattedData);

        if ($response === true) {
            return $this->respond(Response::STATUS_CODE_200, 'Entity updated');
        }

        return $this->unknownError($response);
    }
}
