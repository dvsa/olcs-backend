<?php

/**
 * Companies House Queue Controller
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Olcs\Db\Controller\CompaniesHouse;

use Olcs\Db\Controller\AbstractController;
use Zend\Http\Response;

/**
 * Companies House Queue Controller
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class QueueController extends AbstractController
{
    public function create($data)
    {
        $formattedData = $this->formatDataFromJson($data);

        if ($formattedData instanceof Response) {
            return $formattedData;
        }

        $response = $this->getServiceLocator()->get('CompaniesHouse/Queue')
            ->enqueueActiveOrganisations($formattedData['type']);

        return $this->respond(Response::STATUS_CODE_201, 'Queue Populated', $response);
    }
}
